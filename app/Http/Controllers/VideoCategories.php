<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use DB;
use App\Categories;

class VideoClassController extends Controller
{
    public function getIndex() {
		$source=Categories::leftJoin('post_category','id','=','category_id')
    		->select(DB::raw('categories.*,count(post_category.post_id) as posts_count'))
    		->groupBy('categories.id');
        $filter=DataFilter::source($source);
        $filter->add('title','名稱','text');
        $filter->submit('搜尋');
        $filter->reset('重置');
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('title','名稱',true)->style('width:10%');
		$grid->add('priority','排序',true)->style('width:15%');
        $grid->add('highlight','關注',true)-> cell(function($value, $row) {
        	$hl=[1=>'是',0=>'否'];
			return $hl[$value];
		}) -> style('width:10%');
		$grid->add('posts_count','文章數',true)->style('width:15%');
        $grid->add('created_at','建立時間',true)->style('width:12%');
        $grid->edit('categories/edit','操作','show|modify')->style('width:12%');
        $grid->link('categories/edit',"新增","TR");
        $grid->orderBy('highlight','desc','priority','asc');
        $grid->paginate(config('global.rows_of_page'));
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyEdit() {
        $edit=DataEdit::source(new Categories());
        $edit->link("categories","回列表","TR")->back();
        $edit->add('title','名稱','text')
            -> rule('required|unique:categories,title,' . $edit -> model -> id)
            -> updateValue($edit -> model -> title);
        $edit->add('priority','順序','number');
		$edit->add('highlight','關注','checkbox');
        $edit->add('created_at','建立時間','text')->mode('readonly');
        $edit->add('updated_at','更新時間','text')->mode('readonly');
        return $edit->view('crud.edit',compact('edit'));
    }
}
