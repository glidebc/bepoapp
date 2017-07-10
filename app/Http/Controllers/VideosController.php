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
use App\Videos;
use App\Authors;
use App\Categories;
use App\User;
use App\PostHistories;
use App\Sources;
use App\Video_Class;
use Auth;


class VideosController extends Controller {
	public function getIndex() {
	    $source=Videos::join('showtv.video_class','video.vc_id','=','video_class.vc_id')
            ->select(DB::raw('showtv.video.*'));
		$filter = DataFilter::source($source);
		$filter -> add('v_title', '標題', 'text');
		$categories = ['' => '分類'] + Video_Class::options();
		$filter -> add('vc_id', '分類', 'select') -> options($categories) -> scope(function($query, $value) {
			if (is_null($value)) {
				return $query;
			}
			else {
				return $query  -> where('video_class.vc_id', '=', $value);
			}

		});
		$filter -> add('v_active', '狀態', 'select') -> options([''=>'狀態']+config('global.video_active'));
		$filter -> add('creat_datetime', '建立時間', 'daterange');
		$filter -> submit('搜尋');
		$filter -> reset('重置');
        $filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-bordered table-striped dataTable"));
		$grid -> add('v_title', '標題', true) -> style('width:20%');

		$grid -> add('v_active', '狀態', true) -> cell(function($value, $row) {
			return config('global.video_active')[$value];
		}) -> style('width:10%');


		$grid -> add('vc_id', '分類', true) -> cell(function($value, $row) {
			$options=Video_Class::options();
			 return $options[$value];
		}) -> style('width:12%');
		//$iframe='<iframe src="http://showtv.ctitv.com.tw/v/clickme/%d/%d" width="100%" height="360" scrolling="no" allowfullscreen frameborder="0"></iframe>';
		//copyToClipboard
		$grid -> add('sort', '排序', true) -> style('width:5%');
		$grid -> add('creat_datetime', '建立時間', true) -> style('width:12%');
			$grid -> add('copyTo', '嵌入碼', false) -> cell(function($value, $row) {
				$button=sprintf(
					'<a href="javascript:copyToClipboard(document.getElementById(\'copy-id-%d\'));" >複製</a>',
					$row->auto);
				$date=implode(explode('-',$row->v_date));
				$iframe=sprintf(
					'<iframe src="http://showtv.ctitv.com.tw/v/clickme/%d/%d"  class="ctitv-video-0" width="300" height="169" scrolling="no" allowfullscreen frameborder="0"></iframe>'
					,$date
					,$row->auto);
				$extend=sprintf(
					'<input readonly=true type="text" style="width:80%%;" id="copy-id-%d" value=\'%s\'>',
					$row->auto,$iframe);
				return $button.$extend;
			}) ;

		$grid -> add('v_active', '狀態', true) -> cell(function($value, $row) {
			return config('global.video_active')[$value];
		}) -> style('width:10%');


		$grid -> edit('videos/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> orderBy('video.sort', 'asc');
		$grid -> orderBy('video.creat_datetime', 'desc');
		$grid -> link('videos/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
        //影片編號
		$edit = DataEdit::source(new Videos());
		$edit -> link("videos", "回列表", "TR") -> back();
		$edit -> add('vc_id', '類型', 'select')
			->attr('size',20)
			-> options(Video_Class::options()) -> rule('required');
		$edit -> add('v_title', '標題', 'text') -> rule('required');
		$edit -> add('sort', '排序', 'number') ->insertValue(10)-> rule('required');
        $edit -> add('v_short_title', '短標題', 'text') -> rule('required|max:15');
        $edit -> add('v_date', '播出時間', 'date')
			->insertValue(date('Y-m-d'))
        	-> rule('required');
        $edit -> add('v_episode', '集數', 'number') ->insertValue(1)-> rule('required');
		$edit -> add('source_code_youtube', 'Youtube來源', 'text')
			-> rule('required');
		$edit -> add('v_summary', '簡介'
		, 'App\Fields\Tinymce') -> attr('rows', 15) -> rule('required');
        $edit -> add('v_article', '內容'
        	, 'App\Fields\Tinymce') -> attr('rows', 15) ;

        $edit -> add('v_active', '狀態', 'select')
            ->attr('size',3)
            -> options(config('global.video_active')) -> rule('required');
		$edit -> add('creator', '建立使用者', 'text') -> mode('readonly');
		$edit -> add('creat_datetime', '建立時間', 'text') -> mode('readonly');
		$edit -> add('editor', '更新使用者', 'select') -> mode('readonly');
		$edit -> add('edit_datetime', '更新時間', 'text') -> mode('readonly');

		$edit -> set('creator', Auth::user() -> id);
		$edit -> set('editor', Auth::user() -> id);
		$edit -> set('p_id', 'pg20160202175139');
		$edit -> set('views', '0');

		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
