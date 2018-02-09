<?php

namespace App\Http\Controllers\NewBepoTV;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\NewBepoTV\CategoryModel;
use DB;

class CategoryController extends Controller {
	public function getIndex() {
		$hl=[
			1=>'啟用',
			0=>'關閉'];
		$filter=DataFilter::source(new CategoryModel());
		$filter->add('name','名稱','text');
		//$filter->add('status','狀態','select')->options( array(''=>'狀態')+$hl);
		//$filter->add('status','狀態','select')->options([''=>'狀態']+$hl);
		$filter->submit('搜尋');
		$filter->reset('重置');
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('name','名稱',true)->style('width:10%');
		$grid->add('sort','排序',true)->style('width:15%');
		// $grid->add('color','代表色',true)->cell(function($value,$row) {
		// return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
		// })->style('width:10%');
		// $grid->add('bgcolor','背景顏色',true)->cell(function($value,$row) {
		// return "
		// <div style='border:1px solid #CCC;background-color:$value;'>
		// &nbsp;
		// </div>";
		// })->style('width:10%');
		$grid->add('status','狀態',true)->cell(function($value,$row)  {
		$hl=[
			1=>'啟用',
			0=>'關閉'];
			return array_get($hl,$value);
		})->style('width:10%');
		//$grid->add('article_total','文章數',false)->style('width:15%');
		$grid->edit($this->path.'/edit','操作','show|modify')->style('width:12%');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->orderBy('sort','asc','created_at','desc');
#		$grid->orderBy('_id','asc');
		$grid->paginate(config('global.rows_of_page'));
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$hl=[
		1=>'啟用',
		0=>'關閉'];
		;
		$edit=DataEdit::source(new CategoryModel());
		$edit->link($this->path,"回列表","TR")->back();
		$edit->add('id','序號','text')->mode('readonly');
		$edit->add('name','名稱','text')->rule('required');
		$edit->add('sort','順序','number')->rule('required')->insertValue(100);
		//$edit->add('color','代表色','colorpicker')->rule('required');
		//$edit->add('bgcolor','代表色','colorpicker')->rule('required');
		$edit->add('status','狀態','select')->options($hl)->rule('required');
        $edit->add('link','指定連結','text');
		return $edit->view('crud.edit',compact('edit'));
	}

}
