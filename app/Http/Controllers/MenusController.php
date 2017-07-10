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
use App\User;
use Auth;
use App\Menus;

class MenusController extends Controller {
	public function getIndex() {
		$source=Menus::leftJoin('users','menus.created_user_id','=','users.id')->select(DB::raw('menus.*,users.name as username'));
		$filter=DataFilter::source($source);
		$filter->add('title','名稱','text');
		$filter->add('created_at','建立時間','daterange');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('title','標題',true);
		$grid->add('priority','排序',true)->style('width:20%');
		$grid->add('username','建立使用者',true)->style('width:16%');
		$grid->add('created_at','建立時間',true)->style('width:12%');
		$grid->edit('menus/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('priority','asc');
		$grid->link('menus/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new Menus());
		$edit->link("menus","回列表","TR")->back();
		$edit->add('title','名稱','text')->rule('required');
		$edit->add('priority','排序','number')->rule('required|min:0');
		$edit->add('icon', '圖示', 'App\Fields\FontAwesome') -> rule('required');
		$edit->add('created_user_id','建立使用者','text')->mode('readonly');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->add('updated_user_id','更新使用者','text')->mode('readonly');
		$edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit->set('created_user_id',Auth::user()->id);
		$edit->set('updated_user_id',Auth::user()->id);
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
