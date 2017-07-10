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
use App\Notification;
use App\Posts;
use App\User;
use Auth;

class NotificationController extends Controller {

	private $status=array(
		null=>'',
		''=>'',
		0=>'等待中',
		1=>'處理中',
		2=>'完成',
	);

	public function getIndex() {

		$filter=DataFilter::source(new Notification());
		$filter->add('title','標題','text');
		$filter->add('play_at','推播時間','daterange');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();

		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('play_at','預計推播時間',true);
		$grid->add('title','推送標題',true);
		$grid->add('ios_success','IOS success',true);
		$grid->add('ios_error','IOS error',true);
		$grid->add('android_success','Android success',true);
		$grid->add('android_error','Android error',true);
		$grid->add('status','推送狀態',true)->cell(function($value,$row) {
			return $this->status[$value];
		})->style('width:15%');
		$grid->add('message_id','推送序號',true);
		$grid->edit('notification/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('play_at','desc');
		$grid->orderBy('id','desc');
		$grid->link('notification/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new Notification());
		$edit->link("notification","回列表","TR")->back();
		$edit->add('post.title','文章','autocomplete')->minChars(1)->rule('required')->remote('title',"id",url('search/posts'));
		$edit->add('title','推送標題','text')->rule('required');
		$edit->add('play_at','預計推播時間','datetime')->rule('required');
		$edit->add('message_id','推送序號','text')->mode('readonly');
		$edit->add('begin_at','推送開始時間','text')->mode('readonly');
		$edit->add('end_at','推送結束時間','text')->mode('readonly');
		$edit->add('status','推送狀態','text')->mode('readonly');
		$edit->add('ios_success','IOS success','text')->mode('readonly');
		$edit->add('ios_error','IOS error','text')->mode('readonly');
		$edit->add('android_success','Android success','text')->mode('readonly');
		$edit->add('android_error','Android error','text')->mode('readonly');
		$edit->add('created_user_id','建立使用者','text')->mode('readonly');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->add('updated_user_id','更新使用者','text')->mode('readonly');
		$edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit->set('created_user_id',Auth::user()->id);
		$edit->set('updated_user_id',Auth::user()->id);
		$edit->set('status','0');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
