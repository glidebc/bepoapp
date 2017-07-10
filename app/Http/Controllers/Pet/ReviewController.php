<?php

namespace App\Http\Controllers\Pet;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\Pet\PetData;
use App\Model\Pet\CogiRun;
use Redirect;
//pet_data,cogi_run
//id,name,birthday,email,phone,petname,petage,description,pic_path,created_at,status

class ReviewController extends Controller {
    public $opts=array(
        ''=>'狀態',
        '0'=>'等待審核',
        '1'=>'通過',
        '2'=>'不通過',
    );
    public function getIndex() {
        $model=new PetData();
        $filter=DataFilter::source($model);
        $filter->add('name','參與者','text');
        $filter->add('petname','寵物名稱','text');
        $filter->add('status','狀態','select')->options($this->opts);
        $filter->submit('搜尋');
        $filter->reset('重置');
        $filter->build();

        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
        $grid->add('name','參與者',true)->style('width:11%');
        $grid->add('petname','寵物名稱',true);
        $grid->add('petage','寵物年齡',true)->style('width:11%');
        $grid->add('email','Email',true);
        $grid->add('status','狀態',false)->cell(function($value,$row) {
            return @$this->opts[$value];
        })->style('width:5%');
        $grid->add('phone','手機',true)->style('width:10%');
        $grid->add('pic_path','Photo',false)->cell(function($value,$row) {
            if($value) {
                return "<img width=\"120px\" src=\"$value\">";
            }
        })->style('width:15%');
        $grid->add('review','操作',false)->cell(function($value,$row) {
            $return='';
            if($value) {
                $return.="<img width=\"120px\" src=\"$value\">";
            }
            $return.='<a class="" title="明細資料" href="pet_review/edit?show='.$row['id'].'"><span class="btn-sm btn-info btn">明細</span></a>';
            $return.='&nbsp;<a class="" title="明細資料" href="pet_review/allow?show='.$row['id'].'"><span class="btn-sm btn-success btn">通過</span></a>';
            $return.='&nbsp;<a class="" title="明細資料" href="pet_review/deny?show='.$row['id'].'"><button class="btn-sm btn-danger btn">不通過</button></a>';
            return $return;
        });
        //$grid->add('created_at','建立時間',true);
        //$grid->edit('pet_review/edit','操作','show|delete|modify|confirmed')->style('width:12%');
        $grid->orderBy('created_at','desc');
        $grid->paginate(config('global.rows_of_page'));
        $grid->build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyAllow(Request $request) {
        $id=$request->input('show');
        $model=PetData::find($id);
        $model->status=1;
        $model->save();
        return Redirect::back();
    }

    public function anyDeny(Request $request) {
        $id=$request->input('show');
        $model=PetData::find($id);
        $model->status=2;
        $model->save();
        return Redirect::back();
    }

    public function anyEdit() {
        $model=new PetData();
        $edit=DataEdit::source($model);
        $edit->link("pet_review","回列表","TR")->back();
        $edit->add('name','參與者','text')->rule('required');
        $edit->add('status','狀態','select')->options($this->opts)->rule('required');
        $edit->add('birthday','生日','date')->rule('required');
        $edit->add('email','Email','text')->rule('required|email');
        $edit->add('phone','手機','text')->rule('required');
        $edit->add('petname','寵物名稱','text')->rule('required');
        $edit->add('petage','寵物年齡','text')->mode('readonly');
        $edit->add('description','描述','textarea')->mode('readonly');
        $edit->add('pic_path','Photo', 'App\Fields\ImageView')
             ->preview(320,180);
        $edit->add('created_at','建立時間','text')->mode('readonly');
        $edit->build('crud.dataform');
        return $edit->view('crud.edit',compact('edit'));
    }

}
