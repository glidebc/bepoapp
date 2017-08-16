<?php

namespace App\Http\Controllers\Sticker;

use App\Http\Requests;
use Illuminate\Http\Request;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use Auth;
use App\Model\Sticker\VendorModel;
use App\Helper\OptHelper;
use Input;
use Excel;
use Cache;
use DB;
use Illuminate\Support\Facades\Redis;

class VendorController extends Controller {
	public function getIndex(Request $request) {
		$m=new VendorModel();
		$filter=DataFilter::source($m);
		$filter->add('name','標題','text');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
		$grid->add('name','旅行社',true)->style('width:20%')->cell(function($value,$row) {
			return sprintf("<a href='%s' target='_blank'>%s</a>",$row->url,$value);
		});
		$grid->add('sort','排序',true)->style('width:10%');
		$grid->add('created_at','建立時間',false)->style('width:10%');
		$grid->edit($this->path.'/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('sort','asc');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	private function hitTest($key,$id,$limit=10) {
		Redis::zIncrBy($key,1,$id);
		$keys=Redis::zRevRangeByScore($key,'+inf','-inf',array(
			'withscores'=>false,
			'limit'=> array(
				0,
				$limit
			)
		));
		$vals=array();
		foreach($keys as $k) {
			$score=Redis::zScore($key,$k);
			$vals[]=[
			$k,
			$score*1];
		}
		return array(
			'flds'=> array(
				'id',
				'score'
			),
			'vals'=>$vals
		);
	}

	public function anyAdd(Request $request,$id) {
		return $this->hitTest('sticker.vendor3333',$id,20);
	}

	public function anyInfo(Request $request) {
		$datalist=Cache::remember('sticker.vendor',1,function() {
			$coll=VendorModel::join('vendor_item','vendor_id','=','vendor.id')->orderBy('vendor.sort','asc','vendor_item.sort','asc')->select(DB::raw('vendor_item.id as id,vendor.name as vendor_name,vendor_id,vendor.url as vendor_url,vendor.pic as vendor_pic,vendor.url as vendor_url,vendor_item.title as title'))->get();
			$return=array();
			foreach($coll as $data) {
				$return[$data['vendor_id']]['url']=$data['vendor_url'];
				$return[$data['vendor_id']]['name']=$data['vendor_name'];
				$return[$data['vendor_id']]['pic']=asset('sticker/'.$data['vendor_pic']);
				$return[$data['vendor_id']]['entries'][]=array(
					'id'=>$data['id'],
					'title'=>$data['title'],
					'url'=>$data['url']
				);
			}
			return $return;
		});
		return view('sticker.index',array('datalist'=>$datalist));
	}

	public function anyEdit(Request $request) {
		$edit=DataEdit::source(new VendorModel());
		$edit->link($this->path,"回列表","TR")->back();
		$edit->add('name','旅行社','text')->rule('required');
		$edit->add('sort','排序','number')->insertValue(100)->rule('required');
		$edit->add('url','連結','text')->rule('required');
		$filename=md5(microtime(true));
		$extension='';
		if(Input::hasFile('pic')) {
			$extension=Input::file('pic')->getClientOriginalExtension();
		}
		$edit->add('pic','pic','image')->rule('required')->rule('mimes:jpeg,jpg,png,gif')->preview(100,75)->resize(100,75)->move('sticker/',$filename.'.'.$extension);
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
