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
use Log;
use App\System;
use Carbon\Carbon;

class SystemController extends Controller {
	public function getIndex() {
		$filter=DataFilter::source(new System());
		$filter->add('name','系統名稱','text');
		$filter->add('enabled','啟用狀態','select')->options(config('global.enabled_all'));
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->add('name','名稱',true)->style('width:20%');
		//$grid->add('path','路徑',true);
		$grid->add('description','描述')->filter('strip_tags|mb_substr[0,16]');
		//$grid->add('priority','排序',true);
		$grid->add('enabled','狀態',true)->cell(function($value,$row) {
			return config('global.enabled_all')[$value];
		})->style('width:10%');
		$grid->add('created_at','編修日期',true)->cell(function($value,$row) {
            return Carbon::parse($value)->format('Y-m-d');
        })->style('width:15%');
		$grid->edit('system/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('id','asc');
		$grid->link('system/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new System());
		$edit->link("system","回列表","TR")->back();
		$edit->add('name','名稱','text')->rule('required');
		$edit->add('path','路徑','text');
		$edit->add('description','描述','textarea');
		$edit->add('embed','Embed','textarea');
		//$edit->add('priority','排序','number')->insertValue(100);
		$edit->add('enabled','狀態','select')->options(config('global.enabled'))->rule('required');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
