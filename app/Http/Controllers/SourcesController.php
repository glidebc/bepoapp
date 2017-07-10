<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use DB;
use App\Sources;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;

class SourcesController extends Controller
{
    public function getIndex() {

        $filter=DataFilter::source(new Sources());
        $filter->add('name','名稱','text');
        $filter->add('url','URL','text');
        $filter->add('has_license','版權與否','select')->options(config('global.license_all'));
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('name','名稱',true)->style('width:10%');
        $grid->add('url','URL',true);
		$grid -> add('has_license', '版權', true)  -> cell(function($value, $row) {
			$logtypes=config('global.license_all');
			return $logtypes[$value];
		})-> style('width:10%');

        $grid->add('created_at','建立時間',true)->style('width:12%');
        $grid->edit('sources/edit','操作','show|delete|modify')->style('width:12%');
        $grid->link('sources/add',"新增","TR");
        $grid->orderBy('name','desc');
        $grid->paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyEdit() {
        $edit=DataEdit::source(new Sources());
        $edit->link("sources","回列表","TR")->back();
        $edit->add('name','名稱','text')->rule('required')-> rule('required|unique:sources,name,' . $edit -> model -> id) -> updateValue($edit -> model -> name);;
        $edit->add('url','URL','text')->rule('required')->rule('required');
        $edit->add('has_license','版權與否','select')->options(config('global.license'))->rule('required');
        $edit->add('created_at','建立時間','text')->mode('readonly');
        $edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit -> build('crud.dataform');
        return $edit->view('crud.edit',compact('edit'));
    }
}
