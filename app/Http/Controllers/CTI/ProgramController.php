<?php

namespace App\Http\Controllers\CTI;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use Redirect;
use App\Model\CTI\Program;

class ProgramController extends Controller{
    public function getIndex(Request $request){
        $model=new Program();
        $filter=DataFilter::source($model);
        $filter->add('name','名稱','text');
        $levels=[
                ''=>'等級'
        ]+config('cti.level');
        $filter->add('level','等級','select')->options($levels);
        $filter->submit('搜尋');
        $filter->reset('重置');
        $filter->build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array(
                "class"=>"table table-bordered table-striped dataTable"
        ));
        $grid->add('name','名稱',true)->style('width:20%');
        $grid->add('level','等級',true)->cell(function ($value,$row) use ($levels){
            return array_get($levels,$value);
        })->style('width:15%');
        $grid->add('url','URL',true);
        $grid->add('color','代表色',true)->cell(function ($value,$row){
            return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
        })->style('width:10%');
        $grid->edit($request->segment(1).'/edit','操作','show|modify')->style('width:12%');
        $grid->orderBy('name','asc');
        $grid->link($request->segment(1).'/edit',"新增","TR");
        $grid->paginate(config('global.rows_of_page'));
        $grid->build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }
    public function anyEdit(Request $request){
        $edit=DataEdit::source(new Program());
        $edit->link($request->segment(1),"回列表","TR")->back();
        $edit->add('name','名稱','text')->rule('required');
        $edit->add('level','類型','select')->options(config('cti.level'));
        $edit->add('url','URL','text');
        $edit->add('color','代表色','colorpicker')->rule('required');
        $edit->build('crud.dataform');
        return $edit->view('crud.edit',compact('edit'));
    }
}
