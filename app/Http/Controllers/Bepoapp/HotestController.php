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
use App\Bepoapp\Hotest;

class HotestController extends Controller
{
    public function getIndex() {
		$source=new Hotest();
        $filter=DataFilter::source($source);
        $filter->add('title','名稱','text');
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
		
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('title','標題',true)->style('width:20%');
		$grid->add('thumb_url','圖片',true)-> cell(function($value, $row) {
			return '<img style="width:90%;" src="'.env('APP_URL').'/star_thumbs/'
			.$value.'"';
		}) ->style('width:20%');
		$grid->add('priority','排序',true)->style('width:15%');
        $grid->add('url','連結',true)-> cell(function($value, $row) {
            $short_text=str_limit($value,30,'...');
            return "<a target='_blank' title='$value' href='$value'>$short_text</a>";
        }) ;
        $grid->edit('bepohotest/edit','操作','show|modify|delete')->style('width:12%');
        $grid->link('bepohotest/edit',"新增","TR");
        $grid->orderBy('priority','asc');
        $grid->paginate(config('global.rows_of_page'));
        $grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
    }

    public function anyEdit() {
        $edit=DataEdit::source(new Hotest());
        $edit->link("bepohotest","回列表","TR")->back();
        $edit->add('title','標題','text')
            -> rule('required|unique:bepoapp_star,title,' . $edit -> model -> id)
            -> updateValue($edit -> model -> title);
		$edit->add('thumb_url','縮圖', 'image')
		     ->move('star_thumbs/')
		     ->preview(240,120)-> rule('required');
        $edit->add('priority','排序','number')->insertValue(10)-> rule('required');
		$edit->add('url','連結','text')-> rule('required');
        return $edit->view('crud.edit',compact('edit'));
    }
}
