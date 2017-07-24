<?php

namespace App\Http\Controllers\Sticker;

use App\Http\Requests;
use Illuminate\Http\Request;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Model\Sticker\VendorItemModel;
use App\Model\Sticker\VendorModel;
use App\Helper\OptHelper;
use Input;
use Excel;
use Cache;
use DB;
use Auth;

class VendorItemController extends Controller {
	public function getIndex(Request $request) {
		$m=VendorItemModel::join('vendor','vendor.id','=','vendor_id')->select(DB::raw('vendor_item.*,vendor.name as vendor_name'));
		$filter=DataFilter::source($m);
		$filter->add('title','標題','text');
		$opts=VendorModel::pluck('name','id')->toArray();

		$filter->add('vendor','旅行社','select')->options( array(''=>'旅行社')+$opts)->scope(function($query,$value) {
			if(!is_null($value)) {
				$query=$query->whereIn('vendor_id',array($value));
			}
			return $query;
		});

		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
		$grid->add('title','標題',true)->style('width:20%')->cell(function($value,$row) {
			return sprintf("<a href='%s' target='_blank'>%s</a>",$row->url,$value);
		});
		$grid->add('vendor_name','旅行社',true)->style('width:20%');
		$grid->add('sort','排序',true)->style('width:10%');
		$grid->add('created_at','建立時間',false)->style('width:10%');
		$grid->edit($this->path.'/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('sort','asc');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit(Request $request) {
		$edit=DataEdit::source(new VendorItemModel());
		$edit->link($this->path,"回列表","TR")->back();
		$opts=VendorModel::pluck('name','id')->toArray();
		$edit->add('vendor_id','旅行社','select')->options($opts)->rule('required');
		$edit->add('title','標題','text')->rule('required');
		$edit->add('sort','排序','number')->insertValue(100)->rule('required');
		$edit->add('url','連結','text')->rule('required');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
