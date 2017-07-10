<?php

namespace App\Http\Controllers\Bepoapp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use DB;
use App\Bepoapp\Categories;
use App\Categories as PCategories;

class CategoriesController extends Controller {
	public function getIndex() {
		$source = new Categories();
		$filter = DataFilter::source($source);
		$filter -> add('title', '名稱', 'text');
		$filter -> submit('搜尋');
		$filter -> reset('重置');
		$filter -> build();

		$grid = DataGrid::source($filter);
		$grid -> attributes(array("class" => "table table-striped"));
		$grid -> add('title', '名稱', true) -> style('width:10%');
		$grid -> add('priority', '排序', true) -> style('width:10%');
		$grid -> add('highlight', '關注', true) -> cell(function($value, $row) {
			$hl = [
			1 => '是',
			0 => '否'];
			return $hl[$value];
		}) -> style('width:10%');

		//load all PCategories
		$grid -> add('category_ids', '對應分類', true) -> cell(function($value, $row) {
			$categories = PCategories::options();
			$ids = explode(',', $value);
			$values = array();
			foreach ($ids as $id) {
				if (array_key_exists($id, $categories))
					$values[] = $categories[$id];
			}
			return implode(',', $values);
		});

		$grid -> edit('bepocategories/edit', '操作', 'show|modify|delete') -> style('width:12%');
		$grid -> link('bepocategories/edit', "新增", "TR");
		$grid -> orderBy('priority', 'asc');
		$grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
	}

	public function anyEdit() {
		$edit = DataEdit::source(new Categories());
		$edit -> link("bepocategories", "回列表", "TR") -> back();
		$edit -> add('title', '名稱', 'text') -> rule('required|unique:bepoapp_categories,title,' . $edit -> model -> id) -> updateValue($edit -> model -> title);
		$edit -> add('priority', '順序', 'number');
		$edit -> add('enabled', '啟用', 'checkbox');

		$edit -> add('highlight', '關注', 'checkbox');
		$edit -> add('category_ids', '對應分類', 'multiselect') -> attr('size', 20) -> options(PCategories::options());
		$edit -> add('created_at', '建立時間', 'text') -> mode('readonly');
		$edit -> add('updated_at', '更新時間', 'text') -> mode('readonly');

		return $edit -> view('crud.edit', compact('edit'));
	}

}
