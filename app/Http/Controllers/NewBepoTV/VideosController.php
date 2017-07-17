<?php

namespace App\Http\Controllers\NewBepoTV;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Videos;
use App\Authors;
use App\Categories;
use App\User;
use App\PostHistories;
use App\Sources;
use App\Video_Class;
use Auth;
use App\Model\NewBepoTV\VideoModel;
use App\Model\NewBepoTV\ProgramModel;
use App\Model\NewBepoTV\VideoClassModel;
use App\Model\NewBepoTV\CategoryModel;
use App\Helper\OptHelper;
use Input;
use Excel;
use Cache;

class VideosController extends Controller {
	public function getIndex(Request $request) {
		$m=new VideoModel();
		$filter=DataFilter::source($m);
		$filter->add('title','標題','text');
		$opts=CategoryModel::pluck('_id','name')->toArray();
		$temp=array();
		foreach($opts as $k=>$v) {
			$temp[$v]=$k;
		}
		$js="var program_id=$('#program').val();console.log(program_id);$.ajax({url: 'newbepotv_videos/vcs?ele=video_class&plz=1&id='+program_id, dataType: 'script'});";

		$filter->add('program','節目','select')->options( array(''=>'節目')+OptHelper::getProgram())->scope(function($query,$value) {
			if(!is_null($value)) {
				$query=$query->whereIn('program._id',array($value));
			}
			return $query;
		})->onChange($js);

		$programId=$request->input('program');
		$VCOpts=array_flip(\App\Model\NewBepoTV\VideoClassModel::where('program','=',$programId)->pluck('_id','name')->toArray());
		$VCOpts= array(''=>'分類')+$VCOpts;
		$filter->add('video_class','分類','select')->attr('size',20)->options($VCOpts)->scope(function($query,$value) {
			if(!is_null($value)) {
				$query=$query->whereIn('video_class._id',array($value));
			}
			return $query;
		});

		$filter->add('status','狀態','select')->options( array(''=>'狀態')+config('global.active'));
		$filter->add('created_at','建立時間','daterange')->scope('ofCreateAt');
		$filter->submit('搜尋');
		$filter->reset('重置');
		$filter->build();
		$grid=DataGrid::source($filter);
		$grid->attributes(array("class"=>"table table-bordered table-striped dataTable"));
		$grid->add('title','標題',true)->style('width:20%')->cell(function($value,$row) {
			$href='<a target="_blank" href="%s/video/%s">%s</a>';
			return sprintf($href,env('BEPO_URL'),$row->_id,$value);
		});
		$grid->add('program','節目',true)->cell(function($value,$row) {
			return OptHelper::getProgramName($value);
		})->style('width:10%');
		$grid->add('sort','排序',true)->style('width:10%');

		$grid->add('status','狀態',true)->cell(function($value,$row) {
			return array_get(config('global.active'),$value,'');
		})->style('width:10%');
		$grid->add('hits','觀看次數',true)->style('width:10%')->cell(function($value,$row) {
			return array_get($row,'hits','0');
		});
		$grid->add('hits_plus','觀看次數(+)',true)->style('width:10%')->cell(function($value,$row) {
			return array_get($row,'hits_plus','0');
		});
		//Desktop預設靜音撥放ad,自動停住後影音
		//Mobile顯示縮圖
		$grid->add('copyTo','嵌入碼',false)->cell(function($value,$row) {
			if($row->type=='0') {
				$button=sprintf('<a href="javascript:copyToClipboard(document.getElementById(\'copy-id-%s\'));" >複製</a>',$row->_id);
				$iframe=sprintf('<div class="video-player-wrapper-0"><iframe src="%s/embed/%s" width="300" height="169" scrolling="no" allowfullscreen frameborder="0"></iframe></div>',env('BEPO_URL'),$row->_id);
				$extend=sprintf('<input readonly=true type="text" style="width:80%%;" id="copy-id-%s" value=\'%s\'>',$row->_id,$iframe);
				return $button.$extend;
			}
			else {
				return '&nbsp;';
			}
		})->style('width:10%');

		$grid->add('created_at','建立時間',false)->style('width:10%');
		$grid->edit($this->path.'/edit','操作','show|delete|modify')->style('width:12%');
		$grid->orderBy('id','desc');
		$grid->link($this->path.'/edit',"新增","TR");
		$grid->paginate(config('global.rows_of_page'));
		$grid->build('crud.datagrid');
		return view('crud.grid',compact('filter','grid'));
	}

	/*
	 * "影音編碼" => "vcuna1040429082"
	 "節目編碼" => "pga1040429002"
	 "影片類型編號" => "UNA"
	 "節目名稱+日期" => "大學生20141230"
	 "影片標題" => "2014.12.30大學生了沒完整版　2015全方位運勢預測大補帖！"
	 "影片短標題" => "2015全方位運勢預測大補帖"
	 "本集影片介紹" => "新的一年又要到了，在2014年不管有多少風雨，大家都希望在2015能夠擺脫過去的衰運，在新的一年心想事成，根據研究顯示，每4人就會有1人相信星座，尤其是在新的一年大家都會開始求神拜佛，看開運書，用各種方法來祈求自己好運，所以今天我們就整理出了2015年全方位運勢預測，就算自己的運勢不好還有小撇步可以教你開運現在讓我們趕快來看看2015我們需要注意哪些呢？"
	 "影片上檔日期" => "2014-12-30"
	 "集數" => ""
	 "v_big_pic" => ""
	 "v_small_pic" => ""
	 "Youtube編碼" => "LriPMhV3hPc"
	 "source_code_youku" => ""
	 "source_code_dy" => ""
	 "source_code_vimeo" => ""
	 "source_video_path" => ""
	 "creat_datetime" => ""
	 "creator" => ""
	 "edit_datetime" => ""
	 "editor" => ""
	 "last_ip" => ""
	 "active" => ""
	 "sort" => ""
	 "views" => ""
	 "bk1" => ""
	 "bk2" => ""
	 "bk3" => ""
	 */
	private function loadCSV($file) {
		$array=str_getcsv(file_get_contents($file),"\r\n");
		$dataList=array();
		$header=array();
		$p=0;
		foreach($array as $idx=>$row) {
			$row=str_getcsv($row,",");
			foreach($row as $idx2=>$col) {
				if($idx==0) {
					$header[$idx2]=trim($col);
				}
				else {
					$dataList[$p][$header[$idx2]]=trim($col);
				}
			}
			$p++;
		}
		return $dataList;
	}

	/*
	 "分類(頻道)" => "新聞"
	 "節目名稱" => "新聞深喉嚨"
	 "影片分類" => "全集"
	 "上映日期" => "2017-04-21"
	 "影片長標題(26字)" => "公布年改衝突"查輯專刊"？抓陳進興也不過如此？"
	 "影片短標題(16字)" => "公布年改衝突"查輯專刊"？"
	 "本集影片介紹" => ""
	 "影片上檔日期" => "2017-04-21"
	 "集數" => ""
	 "Youtube編碼" => "IvdugJr-nWo"

	 {
	 "_id":ObjectId("58de442812a52735fa5098ac"),
	 "program":[
	 {
	 "_id":ObjectId("58ca80d612a52747cc610905"),
	 "name":"小明星大跟班"
	 }
	 ],
	 "video_class":[
	 {
	 "_id":ObjectId("58de400c12a52735fa5098a7"),
	 "name":"全集"
	 }
	 ],
	 "title":"小大年終尾牙！諜對諜抽獎大會!",
	 "sort":100,
	 "short_title":"年終尾牙諜對諜抽獎大會！",
	 "date":"2016-12-30",
	 "episode":"3",
	 "yt_id":"CQkAiCOXmaw",
	 "summary":"藝人來賓：沈玉琳、小鐘、Paul、張可昀、大根、蔡允潔、卡古、詹子晴",
	 "article":"藝人來賓：沈玉琳、小鐘、Paul、張可昀、大根、蔡允潔、卡古、詹子晴",
	 "status":1,
	 "views":24,
	 "created":1,
	 "updated":1,
	 "updated_at":   ISODate("2017-05-03T08:35:54   Z"),
	 "created_at":   ISODate("2017-03-31T11:57:28.197   Z"),
	 "hits":135,
	 "tags":[
	 {
	 "_id":ObjectId("58ca806f12a5277543773820"),
	 "name":"有趣"
	 },
	 {
	 "_id":ObjectId("58e63b9112a527647667d7b2"),
	 "name":"熱門"
	 },
	 {
	 "_id":ObjectId("58e695ff12a5277c0d195ba5"),
	 "name":"時尚"
	 },
	 {
	 "_id":ObjectId("58e695ff12a5277c0d195ba6"),
	 "name":"戶外"
	 }
	 ],
	 "type":"0",
	 "length":null,
	 "hits_plus":null
	 }
	 * {
	 "_id" : ObjectId("58cf842812a52778d27ccaf2"),
	 "name" : "美好年代",
	 "categories" : [
	 {
	 "_id" : ObjectId("58d39ae012a52775344a3f25"),
	 "name" : "戲劇"
	 }
	 ],
	 "zone" : [
	 {
	 "_id" : ObjectId("58ca80b812a527478f5d1c5d"),
	 "name" : "台灣"
	 }
	 ],
	 "p_link" : "1",
	 "summary" : "<p>美好年代</p>",
	 "explanation" : "<p>美好年代</p>",
	 "sort" : 311,
	 "status" : 1,
	 "created" : 77,
	 "updated" : 77,
	 "updated_at" : ISODate("2017-04-13T03:20:25.737Z"),
	 "created_at" : ISODate("2017-03-20T07:26:32.107Z"),
	 "promote_pic" : null,
	 "small_pic" : "1e67cdee162b3264256b8a15ffc5608c.jpg"
	 }

	 {
	 "_id" : ObjectId("58ca80df12a527753d44f23f"),
	 "program" : "58de0fc912a52709075fe333",
	 "name" : "全集",
	 "sort" : "100",
	 "status" : "1",
	 "created" : 42,
	 "updated" : 42,
	 "updated_at" : ISODate("2017-03-31T11:36:47.715Z"),
	 "created_at" : ISODate("2017-03-16T12:11:11.336Z")
	 }
	 *
	 */
	public function anyImport(Request $request) {
		return;
		$csvPath=base_path('storage/videos/prepare/*.csv');
		$files=glob($csvPath);
		foreach($files as $file) {
			$dl=array();
			foreach($this->loadCSV($file) as $row) {
				$values=array_values($row);
				$categoryName=$values[0];
				$programName=$values[1];
				$cModel=CategoryModel::where('name','=',$categoryName)->first();
				$pModel=ProgramModel::where('name','=',$programName)->first();
				if(count($cModel)&&count($pModel)) {
					$e=trim($values[8]);
					if(strlen($e)>0) {
						$dl[$programName][$values[2]][$e]=$row;
					}
					else {
						$dl[$programName][$values[2]][]=$row;
					}
				}
			}
			if(count($dl)==0)
				continue;
			foreach($dl as $programName=>$d1) {
				$pModel=ProgramModel::where('name','=',$programName)->first();
				foreach($d1 as $videoClassName=>$d2) {
					//clear video_class
					VideoClassModel::where('name','=',$videoClassName)->where('program','=',$programName)->delete();
					//clear all videos
					VideoModel::whereIn('video_class.name',array($videoClassName))->whereIn('program.name',array($programName))->delete();
					//build new video_class
					$vcModel=new VideoClassModel();
					$vcModel->program=$pModel->_id;
					$vcModel->name=$videoClassName;
					$vcModel->sort=100;
					$vcModel->status=1;
					$vcModel->save();
					//loop to add videos
					foreach($d2 as $episode=>$d3) {
						$data=array_values($d3);
						//   $cModel=CategoryModel::where('name','=',$categoryName)->first();
						$vModel=new VideoModel();
						$vModel->program=$pModel->_id;
						$vModel->title=$data[4];
						$vModel->video_class=$vcModel->_id;
						$vModel->short_title=$data[5];
						$vModel->created_at=$data[7];
						$vModel->hits_plus=0;
						$vModel->hits=0;
						$vModel->views=0;
						$vModel->status=1;
						$vModel->date=$data[3];
						$vModel->summary=$data[6];
						$vModel->article=$data[6];
						$vModel->episode=$data[8];
						$vModel->yt_id=$data[9];
						$vModel->type=0;
						$vModel->sort=100;
						$vModel->length=0;
						$vModel->save();
						//dump($vModel);
						echo $programName.' '.$videoClassName.' '.$data[8].' '.$data[5].'<br/>';
					}
				}
			}
		}
	}

	public function anyVcs(Request $request) {
		$id=$request->input('id');
		$ele=$request->input('ele');

		if($request->has('id')&&$request->has('ele')) {
			$opts=array_flip(\App\Model\NewBepoTV\VideoClassModel::where('program','=',$id)->pluck('_id','name')->toArray());
			$eleOpts='';
			if($request->has('plz')) {
				$eleOpts.='<option value=\"\">分類</option>';
			}
			foreach($opts as $k=>$v) {
				$eleOpts.="<option value=\"$k\">$v</option>";
			}
			$script="$('#$ele').empty();$('#$ele').append('$eleOpts');";
			return $script;
		}
		else {
			$eleOpts='';
			if($request->has('plz')) {
				$eleOpts.='<option value=\"\">分類</option>';
			}
			$script="$('#$ele').empty();$('#$ele').append('$eleOpts');";
			return $script;
		}
	}

	public function anyEdit(Request $request) {

		$this->id=null;
		if($request->has('modify'))
			$this->id=$request->input('modify');
		if($request->has('show'))
			$this->id=$request->input('show');
		//影片編號

		$model=VideoModel::find($this->id);
		if($model==null)
			$model=new VideoModel();

		$edit=DataEdit::source($model);
		$edit->link($this->path,"回列表","TR")->back();
		$js="var program_id=$('#program').val();console.log(program_id);$.ajax({url: 'vcs?ele=video_class&id='+program_id, dataType: 'script'});";
		$edit->add('program','節目','select')->attr('size',20)->options(OptHelper::getProgram())->rule('required')->onChange($js);

		$programId=$model->program?$model->program:array_keys(OptHelper::getProgram())[0];
		$VCOpts=array_flip(\App\Model\NewBepoTV\VideoClassModel::where('program','=',$programId)->pluck('_id','name')->toArray());
		$edit->add('video_class','分類','select')->attr('size',20)->options($VCOpts)->rule('required');
		//$edit->add('title','標題','text')->rule('required');
		$edit->add('title','標題','text')->rule('required|max:26');
		$filename=md5(microtime(true));
		$extension='';
		if(Input::hasFile('small_pic')) {
			$extension=Input::file('small_pic')->getClientOriginalExtension();
		}
		//   $edit->add('small_pic',sprintf('列表專用圖(%sx%s)',env('IMAGE_SMALL_WIDTH'),env('IMAGE_SMALL_HEIGHT')),'text');
		$edit->add('small_pic',sprintf('列表專用圖(%sx%s)',env('IMAGE_SMALL_WIDTH'),env('IMAGE_SMALL_HEIGHT')),'image')->rule('required')->rule('mimes:jpeg,jpg,png,gif')->preview(round(env('IMAGE_SMALL_WIDTH')/2),round(env('IMAGE_SMALL_HEIGHT')/2))->resize(env('IMAGE_SMALL_WIDTH'),env('IMAGE_SMALL_HEIGHT'))->move('newbepotv/video/small/',$filename.'.'.$extension);
		$edit->add('sort','排序','number')->insertValue(100)->rule('required');
		$edit->add('short_title','短標題','text')->rule('required');
		//$edit->add('short_title','短標題','text')->rule('required|max:16');
		$edit->add('date','播出時間','date')->insertValue(date('Y-m-d'))->rule('required');
		$edit->add('episode','集數','number')->rule('required');
		$edit->add('type','來源類型','select')->options(config('global.video_type'))->rule('required');
		$edit->add('yt_id','來源ID','text')->rule('required');
		$edit->add('tags','Tags','App\Fields\Tags');
		$edit->add('length','影片長度(分鐘)','number')->rule('required');
		//$edit->add('summary','簡介','textarea')->attr('rows',15)->rule('required');
		$edit->add('article','內容','textarea')->attr('rows',15)->rule('required');
		$edit->add('status','狀態','select')->attr('size',3)->options(config('global.active'))->rule('required');
		$edit->add('hits_plus','觀看次數(修正值)','number')->rule('required');
		$edit->add('hits','觀看次數','number')->mode('readonly');
		$edit->add('created_at','建立時間','text')->mode('readonly');
		$edit->add('updated_at','更新時間','text')->mode('readonly');
		$edit->set('views','0');
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
