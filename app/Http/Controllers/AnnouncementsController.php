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
use App\Posts;
use App\Authors;
use App\Categories;
use App\User;
use App\PostHistories;
use App\Sources;
use App\Announcements;
use Auth;

class AnnouncementsController extends Controller {
	public function getIndex() {
		$source = Announcements::leftJoin('users', 'announcements.created_user_id', '=', 'users.id') -> select(DB::raw('announcements.*,users.name as username'));
		$users = User::orderBy('name', 'asc') -> pluck('name', 'id');
		$users = ['' => '建立使用者'] + $users -> toArray();
		$filter = DataFilter::source($source);
		$filter -> add('title', '標題', 'text');
		$filter -> add('created_user_id', '建立使用者', 'select') -> options($users);
		$filter -> add('created_at', '建立時間', 'daterange') ;
		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));
		$grid -> add('title', '標題', true) ;
		$grid -> add('username', '建立使用者', true) -> style('width:16%');
		$grid -> add('created_at', '建立時間', true) -> style('width:12%');
		$grid -> edit('announcements/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> orderBy('id', 'desc');
		$grid -> link('announcements/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Announcements());
		$edit -> link("announcements", "回列表", "TR") -> back();
		$edit -> add('title', '標題', 'text') -> rule('required');
		$edit -> add('content', '內容', 'App\Fields\Tinymce') -> rule('required');
		$edit -> add('created_user_id', '建立使用者', 'text') -> mode('readonly');
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_user_id', '更新使用者', 'text') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> set('created_user_id', Auth::user()->id);
		$edit -> set('updated_user_id', Auth::user()->id);
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
