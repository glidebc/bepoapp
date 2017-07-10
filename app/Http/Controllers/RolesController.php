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
use App\Role;
use Log;
use App\RoleUser;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;
use Auth;
use App\Progs;

class RolesController extends Controller {
	public function getIndex() {

		$filter = DataFilter::source(new Role());
		//$filter -> add('name', '名稱', 'text');
		$filter -> add('display_name', '顯示名稱', 'text');
		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));
		$grid -> add('name', '名稱', true) -> style('width:16%');
		//$grid->add('progs','名稱',true)->style('width:20%');
		$grid -> add('display_name', '顯示名稱', true) -> style('width:16%');
		$grid -> add('description', '描述', true);
		$grid -> add('created_at', '建立時間', true) -> style('width:12%');
		$grid -> edit('roles/edit', '操作', 'show|delete|modify') -> style('width:10%');
		$grid -> link('roles/edit', "新增", "TR");
		$grid -> orderBy('name', 'desc');
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Role());
		$edit -> link("roles", "回列表", "TR") -> back();
		$edit -> add('name', '名稱', 'text') -> rule('required');
		$edit -> add('display_name', '顯示名稱', 'text') -> rule('required|min:3');
		$edit -> add('description', '描述', 'textarea');
		$progs = array();
		foreach (Progs::options() as $data) {
			$progs[$data -> menuname][$data -> id] = $data -> name;
		}
		$edit -> add('progs.id', '權限', 'multiselect') -> options($progs) -> attr('size', 20);
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
