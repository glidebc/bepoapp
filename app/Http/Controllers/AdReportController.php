<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\Bepoapp\Ad;
use App\Model\Bepoapp\AdDetail;
class AdReportController extends Controller {
    public function show($id) {
        $data=Ad::where('id','=',$id)->select('*')->firstOrFail();
        return view('ad.show',array('data'=>$data));
    }

    public function getIndex() {
        $platformTypes= array(''=>'平台類型') + config('global.platform_types');
        $filter=DataFilter::source(Ad::customSelect());
        $filter->add('title','委刊名稱(標題)','text');
        $types= array(''=>'類型') + config('global.ad.types');
        $filter->add('is_default','預設','select')->options(array(''=>'預設')+config('global.ad_is_default'));
        $filter->add('type','類型','select')->options($types);
        $filter->add('status','狀態','select')->scope('status')->options(config('global.ad_status'));
        $filter->add('platform_type','平台篩選','select')->options($platformTypes)-> scope(function($query, $value) {
            if (is_null($value)) {
                return $query;
            }
            else {
                return $query-> where('platform_type', 'like', '%'.$value.'%');
            }
        });
        $filter->add('begin_at','開始時間','daterange')->scope('beginat');
        $filter->add('end_at','結束時間','daterange')->scope('endat');
        $filter->submit('搜尋');
        $filter->reset('重置');
        $filter->build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('title','委刊名稱(標題)',true)->cell(function($value,$row) {
            $from=-1;
           $types=explode(',',$row['platform_type']);
            $ptype=$types[0];
            switch($ptype) {
                case '0':
                    $from='bepoweb';
                    break;
                case '1':
                    $from='bepoapp';
                    break;
                case '2':
                    $from='gotvweb';
                    break;
                case '3':
                    $from='gotvapp';
                    break;
            }
            $href=url('adshow/'.$row['id']);
            $href.='?from='.$from;
            return "<a target='_blank' href='$href'>$value</a>";
        })->style('width:20%');
        $grid->add('type','類型',true)->cell(function($value,$row) use ($types) {
            $types=config('global.ad.types');
            return @$types[$value];
        })->style('width:8%');
        $platformTypes= array(''=>'平台') + config('global.platform_types');
        $grid->add('platform_type','平台',true)->cell(function($value,$row) use ($platformTypes) {
              $ret=array();
            $types=explode(',',$value);
           foreach($types as $type){
            if(array_key_exists($type,$platformTypes)) {
                $ret[]= @$platformTypes[$type];
            }   
           } 
            return implode(',',$ret);
        })->style('width:10%');

        // $grid->add('youtube_id','YT id',true)->style('width:8%');
        // $grid->add('image_url','圖片',true)->cell(function($value,$row) {
        // if($value) {
        // return '<img style="width:90%;"
        // src="'.env('APP_URL').'/ad_images/'.$value.'"';
        // }
        // })->style('width:10%');
        // $grid->add('status','狀態',true)->cell(function($value,$row) {
        // return config('global.enabled')[$value];
        // })->style('width:15%');
        $grid->add('show','曝光數',true)->style('width:10%')->style('width:10%');
        $grid->add('click','點擊數',true)->style('width:10%');
        $grid->add('delta','點擊率',false)->style('width:10%');
        $grid->add('st','狀態',true)->cell(function($value,$row) {
            return array_get(config('global.ad_status'),$value,null);
        })->style('width:8%');
        $grid->add('daterange','廣告走期',false)->cell(function($value,$row) {
            return $row['begin_at'].'~'.$row['end_at'];
        });
        $grid->add('days','天數',false)->style('width:8%');
        $grid->add('id','報告',false)->cell(function($value,$row) {
            $url=url('ad_report/download?id=').$row['id'];
            return "<a href='$url'>下載</a>";
        });
        $grid->edit('ad_report/edit','委刊內容(操作)','show');
        $grid->orderBy('id','desc');
        $grid->paginate(config('global.rows_of_page'));
        $grid->build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyDownload(Request $request) {
        $id=$request->input('id');
        $adModel=Ad::findOrFail($id);
        $filename=$adModel->title.date('Y-m-d H:i:s').'.csv';
        $headers=array(
            'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
            'Content-Encoding'=>'UTF-8',
            'Content-type'=>'text/csv',
            'Content-Disposition'=>'attachment; filename='.$filename,
            'Expires'=>'0',
            'Pragma'=>'public'
        );
        $datalist=array( array(
                '時間',
                '曝光',
                '點擊',
                '點擊率'
            ));
        $show=0;
        $click=0;
        foreach(AdDetail::where('ad_id','=',$id)->orderBy('date','asc')->get() as $data) {
            $datalist[]=array(
                $data->date,
                '"'.number_format($data->show).'"',
                '"'.number_format($data->click).'"',
                $data->delta
            );
            $show+=$data->show;
            $click+=$data->click;
        }
        $delta='';
        if($click > 0) {
            $delta=$click / $show * 100;
            $delta=round($delta,2);
            if($delta > 0) {
                $delta.='%';
            } else {$delta='';
            }
        }
        $datalist[]=array(
            '總計',
            '"'.number_format($show).'"',
            '"'.number_format($click).'"',
            $delta
        );
        $output='';
        foreach($datalist as $data) {
            $output.=implode(",",$data)."\n";
        }
        $output="\xEF\xBB\xBF".$output;
        return response($output,200,$headers);
    }

    public function anyEdit() {
        $edit=DataEdit::source(new Ad());
        $edit->link("ad_report","回列表","TR")->back();
        $edit->add('title','委刊名稱(標題)','text')->rule('required');
        // $edit->add('type','類型','select')->options(config('global.adtypes'))->rule('required');edit->add('click_url','點擊連結','text')->rule('required');

        //$edit->add('youtube_id','Youtube id','text');
        //$edit->add('status','狀態','select')->options(config('global.enabled'));
        // $edit->add('image_url','縮圖','image')->move('ad_images/')->rule('mimes:jpeg,jpg,png,gif')->preview(320,480);edit->add('show','曝光數','number');
        $types=config('global.ad.types');
        $edit->add('type','狀態','select')->options($types);
        $platformTypes=config('global.platform_types');
        $edit->add('platform_type','平台','select')->options($platformTypes);
        $edit->add('views','曝光數','text');
        $edit->add('click','點擊數','text');
        $edit->add('delta','點擊率','text');
        $edit->add('days','天數','text');
        $edit->add('daterange','廣告走期','text');
        $edit->add('id','報告','App\Fields\AdList');
        // $edit->add('begin_at','曝光數','datetime')->rule('required');
        // $edit->add('end_at','點擊數','datetime')->rule('required');
        // $edit->add('description','描述','textarea')->rule('required');$edit->build('crud.dataform');
        return $edit->view('crud.edit',compact('edit'));
    }

}
