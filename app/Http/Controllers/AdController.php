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
use Uuid;
use Input;

class AdController extends Controller {
	public function show(Request $request,$id) {
		$data=Ad::where('id','=',$id)->select('*')->firstOrFail();
		$from=$request->input('from');
		$viewFile='ad.show_web';
		if($from=='bepoweb') {
			$viewFile='ad.show_web';
			$data->platform_type=0;
		}
		else
		if($from=='bepoapp') {
			$viewFile='ad.show_app';
			$data->platform_type=1;
		}
		else
		if($from=='gotvweb') {
			$viewFile='ad.show_web';
			$data->platform_type=2;
		}
		else
		if($from=='gotvapp') {
			$viewFile='ad.show_app';
			$data->platform_type=3;
		}
		else {
		}

		if($data) {
			$data->increment('show');
			$adDetail=AdDetail::where('ad_id','=',$data->id)->where('date','=',date('Y-m-d'))->first();
			if(!$adDetail) {
				$adDetail=new AdDetail();
				$adDetail->date=date('Y-m-d');
				$adDetail->ad_id=$data->id;
				$adDetail->show=1;
				$adDetail->click=0;
				$adDetail->save();
			}
			else {
				$adDetail->increment('show');
			}

		}
		$data->from=$from;
		if($request->input('type','html')=='json') {
			if(!$data) {
				return array(
					'result'=>false,
					'data'=> array()
				);
			}
			$ytUlr='https://www.youtube.com/watch?v=';
			$json=array(
				'youtube_url'=>$data->youtube_id?trim($ytUlr.$data->youtube_id):'',
				'youtube_id'=>trim($data->youtube_id),
				'image_url'=>$data->image_url?asset('ad_images/'.$data->image_url):'',
				'click_url'=>$data->click_url?$data->click_url:'',
				'id'=>$data->id,
				'ad_update'=>url('services/ad_update').'?id='.$data->id.'&'.'from='.$data->from
			);
			return array(
				'result'=>true,
				'data'=>$json
			);
		}
		else {
			return view($viewFile,array('data'=>$data));
		}
	}

	public function getIndex() {
		$platformTypes= array(''=>'平台類型')+config('global.platform_types');
		$filter=DataFilter::source(Ad::customSelect());
		$filter->add('title','委刊名稱','text');
		$types= array(''=>'類型')+config('global.ad.types');

		$filter->add('is_default','預設','select')->options( array(''=>'預設')+config('global.ad_is_default'));

		$filter->add('type','類型','select')->options($types);
		$filter->add('status','狀態','select')->scope('status')->options(config('global.ad_status'));
		$filter->add('platform_type','平台篩選','select')->options($platformTypes)->scope(function($query,$value) {
			if(is_null($value)) {
				return $query;
			}
			else {
				return $query->where('platform_type','like','%'.$value.'%');
			}
		});
		$filter->add('begin_at','開始時間','daterange')->scope('daterange','begin_at');
		$filter->add('end_at','結束時間','daterange')->scope('daterange','end_at');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();

		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('title','委刊名稱',true)->cell(function($value,$row) {
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
				case '4':
					$from='demoweb';
					break;
			}
			$href=url('adshow/'.$row['id']);
			$href.='?from='.$from;
			return "<a target='_blank' href='$href'>$value</a>";
		})->style('width:20%');
		$grid->add('type','類型',true)->cell(function($value,$row) use ($types) {
			$types=config('global.ad.types');
			return @$types[$value];
		})->style('width:10%');

		$grid->add('platform_type','平台',true)->cell(function($value,$row) use ($platformTypes) {
			$ret=array();
			$types=explode(',',$value);
			foreach($types as $type) {
				if(array_key_exists($type,$platformTypes)) {
					$ret[]=@$platformTypes[$type];
				}
			}
			return implode(',',$ret);
		})->style('width:10%');

		//$grid->add('youtube_id','Youtube id',true)->style('width:8%');
		$grid->add('image_url','素材(圖片)',true)->cell(function($value,$row) {
			if($value) {
				return '<img style="width:90%;" src="'.env('APP_URL').'/ad_images/'.$value.'"';
			}
		})->style('width:10%');
		$grid->add('st','狀態',true)->cell(function($value,$row) {
			return array_get(config('global.ad_status'),$value);
		})->style('width:8%');
		//$grid->add('begin_at','開始時間',true)->style('width:16%');
		//$grid->add('end_at','結束時間',true)->style('width:16%');
		$grid->add('daterange','廣告走期',false)->cell(function($value,$row) {
			return $row['begin_at'].'~'.$row['end_at'];
		});
		$grid->edit('ad/edit','操作','show|modify');
		$grid->orderBy('begin_at','desc');
		$grid->link('ad/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new Ad());
		$edit->link("ad","回列表","TR")->back();
		$edit->add('title','委刊名稱(標題)','text')->rule('required');
		$edit->add('type','類型','select')->options(config('global.ad.types'))->rule('required');
		$edit->add('vendor','廠商','select')->options(config('global.ad.vendors'))->rule('required_if:type,4,類型,PASSBACK');
		$edit->add('is_default','預設','select')->options(config('global.ad_is_default'))->rule('required');
		$edit->add('count','委託數量','number')->insertValue(100000)->rule('required');
		$edit->add('begin_at','開始時間','datetime')->rule('required');
		$edit->add('end_at','結束時間','datetime')->rule('required|after:begin_at');
		$platformTypes=config('global.platform_types');
		$edit->add('platform_type','平台','multiselect')->attr('size',5)->options($platformTypes)->rule('required');
		$edit->add('youtube_id','Youtube id','text');
		$extension='';
		if(Input::hasFile('image_url')) {
			$extension=Input::file('image_url')->getClientOriginalExtension();
		}
		$edit->add('image_url','素材(圖片)','image')->move('ad_images/')->rule('mimes:jpeg,jpg,png,gif')->preview(320,480)->move('ad_images/',Uuid::generate().'.'.$extension);
		$edit->add('status','狀態','select')->options(config('global.enabled'));
		$edit->add('click_url','點擊連結','text');
		$edit->add('embed','Embed','textarea');
		$edit->add('description','描述','textarea');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
