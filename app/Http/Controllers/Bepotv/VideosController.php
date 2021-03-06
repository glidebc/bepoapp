<?php

namespace App\Http\Controllers\Bepotv;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Ddbepo\Videos;
use App\Ddbepo\Video_Class;
use Auth;
use App\DD360;
use Input;

class VideosController extends Controller {
	
	private $opts=['0'=>'VOD','1'=>'LIVE'];
	private $status=['1'=>'啟用','2'=>'尚未開始','0'=>'停用'];
	public function getIndex() {
		
	    $source=Videos::join('video_class_mapping','video.auto','=','video_class_mapping.auto')
	    	->join('video_class','video_class_mapping.vc_id','=','video_class.vc_id')
            ->select(DB::raw('video.*,video_class_mapping.vc_id'))
			->groupBy('video.auto');
		$filter = DataFilter::source($source);
		$filter -> add('v_title', '標題', 'text');
		$categories = ['' => '分類'] + Video_Class::options();
		$filter -> add('category_id', '分類', 'select') -> options($categories) -> scope(function($query, $value) {
			if (is_null($value)) {
				return $query;
			}
			else {
				return $query  -> where('video_class_mapping.vc_id', '=', $value);
			}
		});
		$filter -> add('v_type', ' 類型', 'select') 
			-> options([''=>'類型']+$this->opts);
		$filter -> add('v_active', '狀態', 'select') 
			-> options([''=>'狀態']+$this->status);
		$filter->submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-bordered table-striped dataTable"));
		$grid -> add('v_title', '標題', true) -> style('width:20%');

		$grid -> add('v_active', '狀態', true) -> cell(function($value, $row) {
			return config('global.video_active')[$value];
		}) -> style('width:10%');

		$grid -> add('v_active', '狀態', true) -> cell(function($value, $row) {
			return $this->status[$value];
		}) -> style('width:10%');

        $grid -> add('v_type', '類型', true) -> cell(function($value, $row) {
            $opts=['0'=>'vod','1'=>'live'];
            return $this->opts[$value];
        }) -> style('width:10%');

		$grid -> add('categories', '分類', false) -> cell(function($value, $row) {
			$buff = array();
			foreach ($value as $cat) {
				$buff[] = $cat -> vc_name;
			}
			return implode(',', $buff);
		}) -> style('width:12%');

		$grid -> add('sort', '排序', true) -> style('width:7%');
		$grid -> add('hash_status', '來源狀態', false) -> cell(function($value, $row) {
			$info=DD360::getInstance()->get($row->source_code_youtube);
			if($info){
				return '<span style="color:green">有效</span>';
			} else {
				return '<span style="color:red">無效</span>';
			}
		})-> style('width:12%');;

		$grid -> add('source_code_youtube', '預設縮圖', false) -> cell(function($value, $row) {
			$info=DD360::getInstance()->get($value);
			if($info){
				return '<img width="120px" src="'.$info['image'].'">';
			} else {
				return '';
			}
		})-> style('width:20%');;
		$grid -> add('v_small_pic', '縮圖', false) -> cell(function($value, $row) {
				return '<img width="120px" src="/bepoapp/video_images/'.$value.'">';
		})-> style('width:20%');;

		$grid -> add('v_active', '狀態', true) -> cell(function($value, $row) {
			return config('global.video_active')[$value];
		}) -> style('width:10%');


		$grid -> edit('bepotvvideos/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> orderBy('video.sort', 'asc');
		$grid -> orderBy('video.creat_datetime', 'desc');
		$grid -> link('bepotvvideos/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
        //影片編號
		$edit = DataEdit::source(new Videos());
		$edit -> link("bepotvvideos", "回列表", "TR") -> back();

		$edit->add('categories','類型','multiselect')
			->attr('size',12)
			->options(Video_Class::options())-> rule('required');

		$edit -> add('v_title', '標題', 'text') -> rule('required');

		$edit->add('thum', '預設縮圖', 'App\Fields\Thumb')->mode('readonly');

		$edit->add('v_small_pic','縮圖', 'image')
		     ->move('video_images/')
			 ->resize(640,360)
		     ->preview(320,180)-> rule('required');
		$edit -> add('sort', '排序', 'number') ->insertValue(10)-> rule('required');
        $edit -> add('v_short_title', '短標題', 'text') -> rule('required|max:32');
        $edit -> add('v_date', '播出時間', 'date')
			->insertValue(date('Y-m-d'))
        	-> rule('required');
        $edit -> add('v_episode', '集數', 'number') ->insertValue(1)-> rule('required');
        $edit->add('v_type','類型','select')
            ->options(['0'=>'vod','1'=>'live']);
		$edit -> add('source_code_youtube', '來源ID', 'text')
			-> rule('required');

		$edit -> add('mobile_url', 'Mobile Url', 'text')
			// ->insertValue(10)
			//->updateValue(10)
			->mode('readonly');;
		// $edit -> add('v_summary', '簡介'
		// , 'App\Fields\Tinymce') -> attr('rows', 15) -> rule('required');
        $edit -> add('v_article', '內容'
        	, 'App\Fields\Tinymce') -> attr('rows', 15) ;

        $edit -> add('v_active', '狀態', 'select')
            ->attr('size',3)
            -> options($this->status) -> rule('required');
		$edit -> add('creator', '建立使用者', 'text') -> mode('readonly');
		$edit -> add('creat_datetime', '建立時間', 'text') -> mode('readonly');
		$edit -> add('editor', '更新使用者', 'select') -> mode('readonly');
		$edit -> add('edit_datetime', '更新時間', 'text') -> mode('readonly');

		$edit -> set('creator', Auth::user() -> id);
		$edit -> set('editor', Auth::user() -> id);
		$edit -> set('p_id', 'pg20160202175139');
		$edit -> set('views', '0');
		$edit -> saved(function($obj) {
			$obj -> model -> sync360();
		});

		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
