<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Auth;
use Log;
use Validator;
use DB;
use App\User;
use App\Role;
use App\RoleUser;

class UsersController extends Controller {
	function __construct() {
		$this->roles=Role::orderBy('id','asc')->pluck('display_name','id');
	}

	public function getIndex() {

		$filter=DataFilter::source(User::join('role_user','users.id','=','role_user.user_id')->join('roles','roles.id','=','role_user.role_id')->select(DB::raw('users.*,roles.id as role_id,roles.display_name as role_display_name')));
		$filter->add('name','名稱','text')->scope(function($query,$value) {
			if(!is_null($value)) {
				$query=$query->where('users.name','like','%'.$value.'%');
			}
			return $query;
		});

		$filter->add('email','電子信箱','text');
		$roles=Role::orderBy('display_name')->pluck('display_name','id');
		$filter->add('role_id','角色','select')->option('','角色')->options($this->roles);

		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('name','名稱',true)->style('width:20%');
		$grid->add('email','電子信箱',true);
		$grid->add('role_display_name','角色',true);
		$grid->add('created_at','建立時間',true)->style('width:12%');
		$grid->edit('users/edit','操作','show|delete|modify')->style('width:10%');
		$grid->link('users/edit',"新增","TR");
		$grid->orderBy('name','desc');
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new User());
		$edit->link("users","回列表","TR")->back();

		if($edit->status=='modify') {
			$edit->add('email','電子信箱','text')->mode('readonly');
			$edit->add('password1','密碼','text')->rule('sometimes|min:8');
		}
		else {
			$edit->add('email','電子信箱','text')->rule('required|email|unique:users,email,'.$edit->model->id)->updateValue($edit->model->email);
			$edit->add('password1','密碼','text')->rule('required|min:8');
		}

		$edit->add('name','名稱','text')->rule('required|unique:users,name,'.$edit->model->id);
		$edit->add('roles.id','角色','select')->options($this->roles)->rule('required');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
