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
use Zofe\Rapyd\Persistence;
use Session;
use Log;
use App\UserHistories;
use App\User;

use App\Progs;

class UserHistoriesController extends Controller {
	public function getIndex() {

		$source=UserHistories::leftJoin('users','user_histories.user_id','=','users.id')->select(DB::raw('user_histories.*,users.name'));
		$progs=Progs::orderBy('name','asc')->pluck('name','path')->toArray();
		$filter=DataFilter::source($source);
		$users=[''=>'編修帳號']+User::lists("name","id")->all();
		$filter->add('user_id','帳號','select')->options($users);
		$filter->add('category','分類','select')->options([''=>'分類']+$progs)->scope(function($query,$value) {
			if(is_null($value)) {
				return $query;
			}
			else {
				return $query->where('category','=','/'.$value);
			}
		});

		$filter->add('created_at','建立時間','daterange');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('created_at','建立時間',true)->style('width:12%');
		$grid->add('name','使用者',true)->style('width:12%');
		$grid->add('category','分類',true)->cell(function($value,$row) use ($progs) {
			$paris=explode('/',substr($value,1),2);
			$key=$paris[0];
			$desc='';
			if(array_key_exists($key,$progs)) {
				$desc.=$progs[$key];
			}
			else {
				$desc.=$key;
			}
			if(count($paris)>1) {
				if($paris[1]=='edit') {$desc.='-編輯';
				}
				else {$desc.=@$pairs[1];
				}
			}
			return $desc;
		})->style('width:10%');
		$grid->add('log','動作')->filter('strip_tags|mb_substr[0,64]');
		$grid->add('ip','IP紀錄',true)->style('width:10%');
		$grid->edit('userhistories/edit','操作','show');
		$grid->orderBy('created_at','desc');
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new UserHistories());
		$edit->link("userhistories","回列表","TR")->back();
		$edit->add('user_id','使用者','select')->options(User::lists("name","id")->all())->rule('required');
		$edit->add('log','動作','textarea')->rule('required');
		$edit->add('category','分類','text');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
