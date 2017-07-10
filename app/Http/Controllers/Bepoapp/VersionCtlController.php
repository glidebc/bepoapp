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
use App\Bepoapp\VersionCtl;

class VersionCtlController extends Controller
{
    public function getIndex() {
		$source=new VersionCtl();	
        $filter=DataFilter::source($source);
        $filter->add('ch','中文名稱','text');
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
		
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('ch','中文名稱',true)->style('width:20%');
		$grid->add('en','英文名稱',true)->style('width:20%');
		$grid->add('os','作業系統',true);
		$grid->add('version','版本',true)->style('width:20%');
        $grid->edit('bepoversionctl/edit','操作','show|modify|delete')->style('width:12%');
        $grid->link('bepoversionctl/edit',"新增","TR");
        $grid->orderBy('id','asc');
        $grid->paginate(config('global.rows_of_page'));
        $grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
    }

    public function anyEdit() {
        $edit=DataEdit::source(new VersionCtl());
        $edit->link("bepoversionctl","回列表","TR")->back();
		$edit->add('ch','中文名稱','text')-> rule('required');
		$edit->add('en','英文名稱','text')-> rule('required');
		$edit->add('os','作業系統','text')-> rule('required');
		$edit->add('version','版本','text')-> rule('required');
        return $edit->view('crud.edit',compact('edit'));
    }
}
