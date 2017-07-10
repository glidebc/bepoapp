<?php

namespace App\Http\Controllers\NewBepoTV;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\NewBepoTV\VideoClassModel;
use DB;
use App\Helper\OptHelper;

class VideoClassController extends Controller {
	public function getIndex() {
		$filter=DataFilter::source(new VideoClassModel());
		$filter->add('name','名稱','text');
		$filter->add('program','節目','select')->options([''=>'節目']+OptHelper::getProgram())->scope(function($query,$value) {
			if(!is_null($value)) {
				return $query->whereIn('program',array($value));
			}
			else {
				return $query;
			}
		});
		$filter->submit('搜尋');
		$filter->reset('重置');
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-striped"));
		$grid->add('program','節目',true)->cell(function($value,$row) {
			return OptHelper::getProgramName($value);
		})->style('width:10%');
		$grid->add('name','名稱',true)->style('width:10%');
		$grid->add('sort','排序',true)->style('width:15%');
		$grid->add('status','狀態',true)->cell(function($value,$row) {
			return array_get(config('global.active'),$value);
		})->style('width:10%');
		$grid->edit($this->path.'/edit','操作','show|modify')->style('width:12%');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->orderBy('program','asc','sort','asc','created_at','desc');
		$grid->paginate(config('global.rows_of_page'));
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit() {
		$edit=DataEdit::source(new VideoClassModel());
		$edit->link($this->path,"回列表","TR")->back();
		$edit->add('program','節目','select')->attr('size',20)->options(OptHelper::getProgram())->rule('required');
		$edit->add('name','名稱','text')->rule('required');
		$edit->add('sort','順序','number')->insertValue(100)->rule('required');
		$edit->add('status','狀態','select')->options(config('global.active'))->rule('required');
		return $edit->view('crud.edit',compact('edit'));
	}

}
