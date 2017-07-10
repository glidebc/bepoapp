<?php

namespace App\Http\Controllers\NewBepoTV;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Videos;
use App\Authors;
use App\Categories;
use App\User;
use App\PostHistories;
use App\Sources;
use App\Video_Class;
use Auth;
use App\Model\NewBepoTV\ZoneModel;

class ZoneController extends Controller {
	public function getIndex() {
		$filter=DataFilter::source(new ZoneModel());
		$filter->add('title','名稱','text');
		$filter->add('status','狀態','select')->options([''=>'狀態']+config('global.active'));
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
		$grid->add('name','名稱',true)->style('width:20%');

		$grid->add('status','狀態',true)->cell(function($value,$row) {
			return array_get(config('global.video_active'),$value);
		})->style('width:10%');

		$grid->edit($this->path.'/edit','操作','show|modify')->style('width:12%');
		$grid->orderBy('name','asc');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new ZoneModel());
		$edit->link($this->path,"回列表","TR")->back();
		$edit->add('id','序號','text')->mode('readonly');
		$edit->add('name','名稱','text')->rule('required');
		$edit->add('status','狀態','select')->attr('size',3)->options(config('global.active'))->rule('required');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
