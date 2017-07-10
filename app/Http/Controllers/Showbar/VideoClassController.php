<?php

namespace App\Http\Controllers\Showbar;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use DB;
use App\Model\Showbar\Videos;
use App\Model\Showbar\Video_Class;

class VideoClassController extends Controller
{
    public function getIndex() {
        $filter=DataFilter::source(Video_Class::where('p_id','=','pg20160202175139'));
        $filter->add('vc_name','名稱','text');
        $filter->submit('搜尋');
        $filter->reset('重置');
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('vc_name','名稱',true)->style('width:10%');
		$grid->add('sort','排序',true)->style('width:15%');
		//$grid->add('color','代表色',true)-> cell(function($value, $row) {
		//	return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
		//}) -> style('width:10%');
		//$grid->add('bgcolor','背景顏色',true)-> cell(function($value, $row) {
		//	return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
		//}) -> style('width:10%');
        $grid->add('active','狀態',true)-> cell(function($value, $row) {
        	$hl=[1=>'是',0=>'否'];
			return $hl[$value];
		}) -> style('width:10%');
		//$grid->add('author','Author','container')
     	//	->view('articles.author_card');
        $grid->edit('showbar_videoclass/edit','操作','show|modify')->style('width:12%');
        $grid->link('showbar_videoclass/edit',"新增","TR");
        $grid->orderBy('sort','asc');
        $grid->paginate(config('global.rows_of_page'));
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyEdit() {
    	$options=[1=>'是',0=>'否'];;
        $edit=DataEdit::source(new Video_Class());
        $edit->link("showbar_videoclass","回列表","TR")->back();
        $edit->add('vc_name','名稱','text')-> rule('required');
            //-> rule('required|unique:cti_onair.video_class,vc_name,' . $edit -> model -> id)
           // -> updateValue($edit -> model -> title);
        $edit->add('sort','順序','number')-> rule('required');
		//$edit->add('color','代表色','colorpicker')-> rule('required');
		//$edit->add('bgcolor','背景顏色','colorpicker')-> rule('required');
		$edit->add('active','狀態','select')->options($options)-> rule('required');
		$edit->set('p_id', 'pg20160202175139');
        return $edit->view('crud.edit',compact('edit'));
    }
}
