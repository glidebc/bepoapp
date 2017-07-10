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
use Input;
use App\PostHistories;
use App\Authors;
use App\Posts;
use App\User;

class PostHistoriesController extends Controller {
    public function __construct() {
        $this->logtypes['']='All';
        $this->logtypes+=config('global.logtypes');
    }

    public function getIndex() {

        $source=PostHistories::leftJoin('posts','post_histories.post_id','=','posts.id',true)
		->leftJoin('users','post_histories.user_id','=','users.id')
        ->select(DB::raw('post_histories.*,posts.title as title,users.name as username'));
	    $filter=DataFilter::source($source);
		$users = ['' => '編修帳號'] + User::lists("name", "id") -> all();
	    $filter -> add('user_id', '編修帳號', 'select') -> options($users);
        $filter->add('title','標題','text');
        $filter->add('created_at','建立時間','daterange');
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('title','標題',true);
        $grid->add('version','版次',true)->style('width:10%');
        $grid->add('author_id','作者',true)->cell(function($value,$row) {
            return "{$row->author->name}";
        })->style('width:10%');
		$grid->add('username','編修帳號',true)->style('width:20%');
        $grid->add('created_at','編修時間',true)->style('width:12%');
        $grid->edit('post_histories/edit','操作','show')->style('width:12%');
        $grid->orderBy('created_at','desc');
        $grid->paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }

    public function anyEdit() {
        $edit=DataEdit::source(new PostHistories());
        $edit->link("post_histories","回列表","TR")->back();
        $edit->add('post_id','標題','select')->options(Posts::lists("title","id")->all());
        $edit->add('version','版次','text')->rule('required');
        $edit->add('author_id','作者','select')->options(Authors::lists("name","id")->all());
        $edit->add('content','內容','App\Fields\Tinymce')->attr('rows',25)->rule('required');
        $edit->add('created_at','建立時間','text')->mode('readonly');
        $edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit -> build('crud.dataform');
        return $edit->view('crud.edit',compact('edit'));
    }

}
