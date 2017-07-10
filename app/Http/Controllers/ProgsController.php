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
use App\Progs;
use App\Menus;
use App\System;


class ProgsController extends Controller {
	public function getIndex() {
		$menus = Menus::orderBy('priority', 'asc') -> pluck('title', 'id');
		$menus = array('' => '目錄') + $menus -> toArray();
		$source = Progs::join('menus', 'progs.menu_id', '=', 'menus.id')
			-> select(DB::raw('progs.*,menus.title as menutitle'));

		$filter = DataFilter::source($source);
		$filter -> add('name', '名稱', 'text')->scope( function ($query, $value) {
            if($value){
                return $query->where('name', 'like',"%$value%");
            }else{
               return $query;
            }
        });
		$filter -> add('menu_id', '目錄', 'select')->options($menus)->scope( function ($query, $value) {
		    if($value){
                return $query->where('progs.menu_id', '=',$value);
            }else {
                return $query;
            }
        });
		$filter -> add('enabled', '狀態', 'select') -> options(config('global.enabled_all'));

		$filter -> submit('搜尋');
		$filter -> reset('重置');
        $filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));

		$grid -> add('name', '名稱', true) -> style('width:18%');
		$grid -> add('menutitle', '目錄', true);
		$grid -> add('path', '路徑', true);
		$grid -> add('priority', '排序', true) -> style('width:12%');
		$grid -> add('enabled', '狀態', true) -> cell(function($value, $row) {
			return config('global.enabled_all')[$value];
		}) -> style('width:10%');
		$grid -> add('updated_at', '更新時間', true) -> style('width:12%');

		$grid -> edit('progs/edit', '操作', 'show|delete|modify') -> style('width:10%');
		$grid -> orderBy('priority', 'asc');

		$grid -> link('progs/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));
        $grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$menus = Menus::orderBy('priority', 'asc') -> pluck('title', 'id');
		$systems = System::orderBy('priority', 'asc') -> pluck('name', 'id');

		$edit = DataEdit::source(new Progs());
		$edit -> link("progs", "回列表", "TR") -> back();
		$edit -> add('name', '名稱', 'text') -> rule('required');
		$edit -> add('system_id', '所屬系統', 'select') -> options($systems) -> rule('required');
		$edit -> add('menu_id', '目錄', 'select') -> options($menus) -> rule('required');
		$edit -> add('path', '路徑', 'text') -> rule('required');
		$edit -> add('priority', '排序', 'number') -> rule('required');
		$edit -> add('enabled', '啟用狀態', 'select') -> options(config('global.enabled'));
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
