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
use App\Live;
use App\User;
use App\Categories;
use App\Sources;

class LiveController extends Controller {
	public function getIndex() {
		$filter = DataFilter::source(new Live());
		$filter -> add('title', '標題', 'text');
		$filter -> add('enabled', '狀態', 'select') -> options(config('global.enabled_all'));
		$filter -> add('begin_at', '上架時間', 'daterange');
		$filter -> add('end_at', '下架時間', 'daterange');
		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();

		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));
		$grid -> add('title', '標題', true) -> style('width:20%');
		$grid -> add('enabled', '狀態', true) -> cell(function($value, $row) {
			return config('global.enabled')[$value];
		}) -> style('width:10%');
		$grid -> add('priority', '排序', true) -> style('width:20%');
		$grid -> add('begin_at', '上架時間', true) -> style('width:20%');
		$grid -> add('end_at', '下架時間', true) -> style('width:20%');

		$grid -> edit('live/edit', '操作', 'show|delete|modify');
		$grid -> orderBy('id', 'desc');
		$grid -> link('live/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Live());
		$edit -> link("live", "回列表", "TR") -> back();
		$edit -> add('title', '標題', 'text') -> rule('required');
		$edit -> add('source_id', '來源', 'select') -> options(Sources::lists("name", "id") -> all()) -> rule('required');
		$edit -> add('category_id', '分類', 'select') -> options(Categories::lists("title", "id") -> all()) -> rule('required');
		$edit -> add('description', '介紹', 'text') -> rule('required');
		$edit -> add('comment', '預計直播時間(說明)', 'text') -> rule('required');
		$edit -> add('url', 'Web圖片位置', 'text') ;
		$edit -> add('app_url', 'APP圖片位置', 'text') ;
		$edit -> add('yt_id', 'Youtube影片ID', 'text') ;
		$edit -> add('fb_id', 'FB直播ID', 'text');
		$edit -> add('paused_comment', '直播暫停說明', 'text') -> rule('required');
		$edit -> add('begin_at', '上架時間',  'datetime') ;
		$edit -> add('end_at', '下架時間',  'datetime') ;
		$edit -> add('priority', '排序值', 'number') -> rule('required');
		$edit -> add('enabled', '狀態', 'select') -> options(config('global.enabled'));
		$edit -> add('created_user_id', '建立使用者', 'select') -> options(User::lists("name", "id") -> all())-> mode('readonly');
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_user_id', '更新使用者', 'select') -> options(User::lists("name", "id") -> all())-> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
