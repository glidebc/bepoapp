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
use Auth;

class PostsController extends Controller {
	public function getIndex() {
		
		$filter = DataFilter::source(new Posts());
		$filter -> add('title', '標題', 'text');
		$authors = ['' => '作者'] + Authors::options();
		$filter -> add('author_id', '作者', 'select') -> options($authors);
		$categories = ['' => '分類'] + Categories::options();
		$filter -> add('category_id', '分類', 'select') -> options($categories) -> scope(function($query, $value) {
			if (is_null($value)) {
				return $query;
			}
			else {
				return $query -> join('post_category', 'id', '=', 'post_id') -> where('category_id', '=', $value);
			}
		});
		$sources = ['' => '來源'] + Sources::options();
		$filter -> add('source_id', '來源', 'select') -> options($sources) -> scope(function($query, $value) {
			if (is_null($value)) {
				return $query;
			}
			else {
				return $query -> where('source_id', '=', $value);
			}
		});
		$filter -> add('status', '狀態', 'select') -> options(config('global.post_status_all'));
		$filter -> add('post_at', '發布時間', 'daterange');
		$filter -> submit('搜尋');
		$filter -> reset('重置');
        $filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-bordered table-striped dataTable"));
		$grid -> add('title', '標題', true)  -> cell(function($value, $row) {
			$secs=strtotime($row->post_at);
			$href=env('BEPOAPP_URL').'/articles/'.date('Ymd',$secs).'/'.$row->id.'/';
			return "<a target='_blank' href='$href'>$value</a>";
		}) -> style('width:20%');
		$grid -> add('status', '狀態', true) -> cell(function($value, $row) {
			return config('global.post_status_all')[$value];
		}) -> style('width:10%');

		$grid -> add('categories', '分類', true) -> cell(function($value, $row) {
			$buff = array();
			foreach ($value as $cat) {
				$buff[] = $cat -> title;
			}
			return implode(',', $buff);
		}) -> style('width:12%');

		//$grid -> add('content', '文章內容') -> filter('strip_tags|mb_substr[0,16]');
		$grid -> add('author_id', '作者', true) -> cell(function($value, $row) {
			return "{$row->author->name}";
		}) -> style('width:12%');
		$grid -> add('hits', '點擊數', true) -> style('width:12%');
		$grid -> add('priority', '排序', true) -> style('width:12%');
		$grid -> add('post_at', '發布時間', true) -> style('width:12%');
		$grid -> edit('posts/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> orderBy('priority', 'asc');
		$grid->orderBy('post_at', 'desc');
		$grid -> link('posts/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));

		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {

		$edit = DataEdit::source(new Posts());
		$edit -> link("posts", "回列表", "TR") -> back();
		$edit -> add('title', '標題', 'text') -> rule('required');
		$edit -> add('type', '格式', 'select') -> options(config('global.post_types')) -> rule('required');
		$edit -> add('categories', '分類', 'multiselect')
			->attr('size',20)
			-> options(Categories::options()) -> rule('required');
		$edit -> add('tags.name', 'Tags', 'tags')
			//->search("name");
			->remote('name', "tag_id", url('search/tags'));
			
		$edit -> add('source_id', '來源', 'select') -> options(Sources::options()) -> rule('required');
		$edit -> add('title', '標題', 'text') -> rule('required');
		$edit -> add('content', '內容', 'App\Fields\Tinymce') -> attr('rows', 25) -> rule('required');
		$author = Authors::orderBy("name", 'asc') -> where('user_id', '=', Auth::user() -> id) -> pluck('name', 'id');
		$edit -> add('author_id', '作者', 'select') -> options($author) -> rule('required');
		$edit -> add('image_url', '圖片位置', 'image');
		$edit -> add('yt_id', 'YT影片ID', 'text');
		$edit -> add('fb_id', 'FB影片ID', 'text');
		$edit -> add('dm_id', 'DM影片ID', 'text');
		$edit -> add('yk_id', 'YK影片ID', 'text');
		$edit -> add('vimdo_id', 'Vimeo影片ID', 'text');
		$edit -> add('vr_id', 'VR影片ID', 'text');
		$edit -> add('version', '版次', 'number') -> insertValue(1) -> mode('readonly');
		$edit -> add('priority', '排序', 'number') -> rule('required');
		$edit -> add('is_notification', '是否推播', 'select') -> options(config('global.yesno')) -> rule('required');
		$edit -> add('is_carousel', '是否跑馬', 'select') -> options(config('global.yesno')) -> rule('required');
		$edit -> add('status', '狀態', 'select') -> options(config('global.post_status'));
		$edit -> add('created_user_id', '建立使用者', 'text') -> mode('readonly');
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_user_id', '更新使用者', 'select') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');

		$edit -> set('created_user_id', Auth::user() -> id);
		$edit -> set('updated_user_id', Auth::user() -> id);

		$edit -> saved(function($obj) {
			$obj -> model -> pushHistoryVersion();
		});
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
