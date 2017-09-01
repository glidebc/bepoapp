<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Authors;
use App\User;
use Log;

class AuthorsController extends Controller {
	public function __construct() {
	}

	public function getIndex() {
		$filter = DataFilter::source(new Authors());
		$filter -> add('name', '名稱', 'text');
		$users = ['' => '使用者'] + User::lists("name", "id") -> all();
		$filter -> add('user_id', '使用者', 'select') -> options($users);

		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));
		$grid -> add('name', '名稱', true);
		$grid -> add('user_id', '使用者', true) -> cell(function($value, $row) {
			return "{$row->user->name}";
		}) -> style('width:18%');
		$grid -> add('created_at', '建立時間', true) -> style('width:12%');
		$grid -> edit('authors/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> link('authors/edit', "新增", "TR");
		$grid -> orderBy('id', 'asc');
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Authors());
		$edit -> link("authors", "回列表", "TR") -> back();
		$edit -> add('name', '名稱', 'text') -> rule('required|unique:authors,name,' . $edit -> model -> id) -> updateValue($edit -> model -> name);
		$edit -> add('user_id', '管理人員編號', 'select') -> options(User::lists("name", "id") -> all());
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
