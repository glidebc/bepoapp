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
use Auth;
use Conner\Tagging\Model\Tag;

class TagsController extends Controller {
	public function getIndex() {
		$filter = DataFilter::source(new Tag());
		$filter -> add('name', 'Name', 'text') ;
		$filter -> add('slug', 'Slug', 'text') ;
		$filter -> submit('搜尋');
		$filter -> reset('重置');
        $filter -> build();
		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-bordered table-striped dataTable"));
		$grid -> add('name', 'Name', true) ;
		$grid -> add('slug', 'Slug', true);
		$grid -> add('count', 'Count', true) ;
		$grid -> edit('tags/edit', '操作', 'show|delete|modify') -> style('width:12%');
		$grid -> orderBy('name', 'asc');
		$grid -> link('tags/edit', "新增", "TR");
		$grid -> paginate(config('global.rows_of_page'));

		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Tag());
		$edit -> link("tags", "回列表", "TR") -> back();
		$edit -> add('name', 'Name', 'text') -> rule('required');
		$edit -> add('slug', 'Slug', 'text') -> rule('required');
		$edit -> build('crud.dataform');
		return $edit -> view('crud.edit', compact('edit'));
	}

}
