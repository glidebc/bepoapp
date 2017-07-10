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
use App\Carousel;
use App\User;
use App\Posts;

class CarouselController extends Controller {
	public function getIndex() {
		$filter = DataFilter::source(new Carousel());
		$filter -> add('title', '標題', 'text');
		//$authors = ['' => '作者'] + Authors::lists("name", "id") -> all();
		//$filter -> add('author_id', '作者', 'select') -> options($authors);
		$filter -> add('enabled', '狀態', 'select') -> options(config('global.enabled_all'));
		$filter -> add('begin_at', '上架時間', 'daterange') ;
		$filter -> add('end_at', '下架時間', 'daterange') ;

		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));

		$grid -> add('title', '標題', true) -> style('width:20%');
		$grid -> add('hits', '點擊數', true) -> style('width:20%');
		$grid -> add('enabled', '狀態', true) -> cell(function($value, $row) {
			return config('global.enabled')[$value];
		}) -> style('width:10%');

		$grid -> add('begin_at', '上架時間',true) ;
		$grid -> add('end_at', '下架時間',true) ;
		$grid -> edit('carousel/edit', '操作', 'show|delete|modify');
		$grid -> orderBy('id', 'desc');
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Carousel());
		$edit -> link("carousel", "回列表", "TR") -> back();
		$edit -> add('post_id', '文章編號', 'text') -> mode('readonly');
		$edit -> add('title', '標題', 'text') -> rule('required');
		$edit -> add('url', '連結', 'text') -> mode('readonly');
		$edit -> add('hits', '點擊數', 'text') -> mode('readonly');
		$edit -> add('begin_at', '上架時間', 'datetime')  -> rule('required');
		$edit -> add('end_at', '下架時間', 'datetime')  -> rule('required');
		$edit -> add('enabled', '狀態', 'select') -> options(config('global.enabled'));
		$edit -> add('created_user_id', '建立使用者', 'select') -> options(User::lists("name", "id") -> all()) -> mode('readonly');
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_user_id', '更新使用者', 'select') -> options(User::lists("name", "id") -> all()) -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
