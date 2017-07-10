<?php

namespace App\Http\Controllers\NewBepoTV;

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
use App\Model\NewBepoTV\ProgramModel;
use App\Helper\OptHelper;
use Input;

class ProgramController extends Controller {
	public function getIndex() {
		$filter=DataFilter::source(new ProgramModel());
		$filter->add('name','標題','text');
		$categories=[''=>'分類']+OptHelper::getCategory();
		$filter->add('categories','分類','select')->options($categories)->scope(function($query,$value) {
			if(is_null($value)) {
				return $query;
			}
			else {
				return $query->whereIn('categories._id',array($value));
			}
		});
		$filter->add('status','狀態','select')->options([''=>'狀態']+config('global.active'));
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
		$grid->add('name','名稱',true)->style('width:20%');
		$grid->add('categories','分類',true)->cell(function($value,$row) {
			return OptHelper::getCategoryName($value);
		})->style('width:10%');
		$grid->add('zone','區域',true)->cell(function($value,$row) {
			return OptHelper::getZoneName($value);
		})->style('width:10%');
		$grid->add('sort','排序',true)->style('width:5%');
		$grid->add('status','狀態',true)->cell(function($value,$row) {
			return array_get(config('global.video_active'),$value);
		})->style('width:10%');
		$grid->add('created_at','建立時間',true)->style('width:12%');
		$grid->edit($this->path.'/edit','操作','show|modify')->style('width:12%');
		$grid->orderBy('sort','asc','created_at','desc');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	// CREATE TABLE `program` (
	// `id` int(11) UNSIGNED NOT NULL,
	// `p_id` varchar(16) NOT NULL COMMENT '節目編號',
	// `kind_id` int(11) NOT NULL COMMENT '整體分類',
	// `zone_id` int(11) NOT NULL COMMENT '區域分類',
	// `p_name` varchar(50) NOT NULL COMMENT '節目名稱',
	// `p_summary` text NOT NULL COMMENT '節目介紹',
	// `p_explanation` varchar(200) NOT NULL COMMENT '補充說明',
	// `promote_pic` varchar(100) NOT NULL COMMENT '大型廣告圖片位置',
	// `small_pic` varchar(100) NOT NULL COMMENT '一般廣告圖片位置',
	// `p_link` varchar(100) DEFAULT NULL COMMENT '指定連結',
	// `creat_datetime` datetime NOT NULL COMMENT '建立日期',
	// `creator` int(11) NOT NULL COMMENT '建立者',
	// `edit_datetime` datetime NOT NULL COMMENT '修改日期',
	// `editor` int(11) NOT NULL COMMENT '修改者',
	// `last_ip` varchar(15) NOT NULL COMMENT '修改者ip',
	// `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '啟用情況',
	// `sort` int(2) NOT NULL DEFAULT '5' COMMENT '排序',
	// `p_views` int(11) NOT NULL DEFAULT '10' COMMENT '觀看數',
	// `bk1` int(11) DEFAULT NULL,
	// `bk2` int(11) DEFAULT NULL,
	// `bk3` int(11) DEFAULT NULL
	// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='節目內容表';
	function anyEdit() {
		$edit=DataEdit::source(new ProgramModel());
		$edit->link($this->path,"回列表","TR")->back();
		$edit->add('id','序號','text')->mode('readonly');
		$edit->add('name','名稱','text')->rule('required');
		$edit->add('categories','分類','select')->options(OptHelper::getCategory(false))->rule('required');
		$edit->add('zone','區域','select')->options(OptHelper::getZone())->rule('required');
		//->preview(64,64)
		$filename=md5(microtime(true));
		$extension='';
		if(Input::hasFile('promote_pic')) {
			$extension=Input::file('promote_pic')->getClientOriginalExtension();
		}
		$edit->add('promote_pic',sprintf('列表專用圖(%sx%s)',env('IMAGE_PROMOTE_WIDTH'),env('IMAGE_PROMOTE_HEIGHT')),'image')->rule('mimes:jpeg,jpg,png,gif')->preview(round(env('IMAGE_PROMOTE_WIDTH')/2),round(env('IMAGE_PROMOTE_HEIGHT')/2))->resize(env('IMAGE_PROMOTE_WIDTH'),env('IMAGE_PROMOTE_HEIGHT'))->move('newbepotv/program/promote/',$filename.'.'.$extension);

		$filename=md5(microtime(true));
		$extension='';
		if(Input::hasFile('small_pic')) {
			$extension=Input::file('small_pic')->getClientOriginalExtension();
		}
		$edit->add('small_pic',sprintf('列表專用圖(%sx%s)',env('IMAGE_SMALL_WIDTH'),env('IMAGE_SMALL_HEIGHT')),'image')->rule('required')->rule('mimes:jpeg,jpg,png,gif')->preview(round(env('IMAGE_SMALL_WIDTH')/2),round(env('IMAGE_SMALL_HEIGHT')/2))->resize(env('IMAGE_SMALL_WIDTH'),env('IMAGE_SMALL_HEIGHT'))->move('images/program/small/',$filename.'.'.$extension);
		$edit->add('summary','簡介','textarea')->rule('required');
		$edit->add('p_link','指定連結','text');
		$edit->add('embed','Embed','textarea');
		$edit->add('sort','排序','text')->rule('required');
		$edit->add('status','狀態','select')->options(config('global.active'))->rule('required');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
