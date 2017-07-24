<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use Input;
use Carbon\Carbon;
use DB;
use App\Posts;
use App\Bepoapp\Categories;
use App\Bepoapp\Live;
use App\Bepoapp\NewEvent;
use App\Bepoapp\Star;
use App\Bepoapp\AppName;
use App\Ddbepo\Videos;
use App\Bepoapp\Hotest;
use App\Notification;
use App\Model\Bepoapp\Ad;
use App\Model\Bepoapp\AdDetail;
use DateHelper;
use Cache;
use App\DD360;
use Log;

class BepoAPIController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	private function getNewsByCategory($kind,$limit=0) {
		$category=Categories::find($kind);
		if(count($category)>0) {
			$ids=$category->category_ids;
			$query=Posts::latest()->groupBy('id')->whereRaw("id in ( select post_id from post_category where category_id in ($ids)  )")->select('*')->orderBy('post_at','desc');
			if($limit>0) {
				$query->take($limit);
			}
			if(Input::get('debug')) {
				$queries=DB::getQueryLog();
				dd($query);
			}
			return $query->get();
		}
		return array();
	}

	private function insert_ad(&$datalist) {
		$len=count($datalist);
		$ad=array('adid'=>'gid');
		$s=0;
		foreach(array(5,13,21,29) as $i) {
			if($len>=$i) {
				$datalist->splice($i+$s,1,[$ad]);
				$s++;
			}
		}
	}

	public function news_test() {
		$datalist=array();
		if(Input::get('kind')>0) {
			$kind=Input::get('kind');
			$key='kind.'.$kind;

			if(Cache::has($key)) {
				$datalist=Cache::get($key);
			}
			else {
				//select posts.* from posts where posts.id in ( select post_id from post_category where post_category.category_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22) ) order by posts.priority asc , posts.created_at desc
				//select posts.* from posts inner join post_category on post_category.post_id = posts.id where post_category.category_id in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22) order by posts.priority asc , posts.created_at desc
				$datalist=$this->getNewsByCategory(Input::get('kind'));
				$count=count($datalist);
				if(Input::get('ver')==1)
					$this->insert_ad($datalist);
				if($count>0) {
					Cache::put($key,$datalist,1);
				}
			}
		}
		else
		if(Input::get('search')) {
			$datalist=Posts::latest()->where('posts.content','like','%'.Input::get('search').'%')->orWhere('posts.title','like','%'.Input::get('search').'%')->get();
		}
		return Response::json(array(
			'result'=>count($datalist)>0,
			'data'=>$datalist
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function notification() {
		$posts=array();
		foreach(Notification::enabled()->groupBy('post_id')->orderBy('play_at','desc')->take(50)->get() as $data) {
			if(!is_null($data->post)) {
				$posts[]=$data->post;
			}
		}
		return Response::j(count($posts)>0,$posts);
	}

	//{"result":true,"data":{"adid":"1471234004","type":"image","click_url":"","ad_url":"http://www.ctitv.com.tw/ctiapp/bannerhtm/1471234004.htm"}}

	public function ad_update(Request $request) {
		$return=array(
			'result'=>false,
			'data'=> array(),
			'error'=>''
		);
		$from=$request->input('from');
		$id=$request->input('id');
		if(!empty($from)&&!empty($id)) {
			$model=Ad::find($id);
			if(count($model)) {
				$model->increment('click');
				$return['result']=true;
				//update by date
				$adDetail=AdDetail::where('ad_id','=',$id)->where('date','=',date('Y-m-d'))->first();
				if(!count($adDetail)) {
					$adDetail=new AdDetail();
					$adDetail->date=date('Y-m-d');
					$adDetail->ad_id=$id;
					$adDetail->click=1;
					$adDetail->show=0;
					$adDetail->save();
				}
				else {
					$adDetail->increment('click');
				}
				//$logFile='ad.log';
				//Log::useDailyFiles(storage_path().'/logs/ad/'.$logFile);
				//Log::info(json_encode($_SERVER,JSON_UNESCAPED_UNICODE));
			}
			else {
				$return['error']='data not found';
			}
		}
		return $return;
	}

	/*
	 * 取得隨機比例
	 * @param array $array
	 * @return int $id
	 *
	 */
	private function getRandKey($array) {
		$tot=0;
		foreach($array as $id=>$size) {
			$tot+=$size;
		}
		$idx=0;
		$result=array();
		foreach($array as $id=>$size) {
			$num=ceil($size/$tot*100);
			$result+=array_fill($idx,$num,$id);
			$idx+=$num;
		}
		if(count($result)<1) {
			return 0;
		}
		$i=array_rand($result,1);
		return $result[$i];
	}

	public function ad(Request $request) {
		//default bepoapp
		//0=bepoweb
		//1=bepoapp
		$from=$request->input('from','bepoapp');
		$platform_type=null;
		if($from=='bepoweb') {
			$platform_type=0;
		}
		else
		if($from=='bepoapp') {
			$platform_type=1;
		}
		else
		if($from=='gotvweb') {
			$platform_type=2;
		}
		else
		if($from=='gotvapp') {
			$platform_type=3;
		}
		else
		if($from=='demoweb') {
			$platform_type=4;
		}
		else {
			$logFile='ad.log';
			//Log::useDailyFiles(storage_path().'/logs/ad/'.$logFile);
			//Log::info(\App\Helper\NetHelper::getIP().' '.$from,$request->all());
		}
		$data=array();
		if(strlen($platform_type)) {
			$now=date('Y-m-d H:i:s');
			//Get by random
			$data=Ad::where('status','=','1')->where('begin_at','<',$now)->where('end_at','>',$now)->where('platform_type','like','%'.$platform_type.'%')->where('is_default','=','0')->select('*')->orderBy(DB::raw('RAND()'))->first();
			if(count($data)>0) {
				//$logFile='ad.log';
				//Log::useDailyFiles(storage_path().'/logs/ad/'.$logFile);
				//Log::info(json_encode($_SERVER,JSON_UNESCAPED_UNICODE));
				$embed=$data->embed;
				$data=$data->toArray();
				$data['embed']=$embed;
				$data['ad_url'].='?from='.$from;
			}
			else {
				//get default
				$data=Ad::where('status','=','1')->where('begin_at','<',$now)->where('end_at','>',$now)->where('platform_type','like','%'.$platform_type.'%')->where('is_default','=','1')->select('*')->orderBy(DB::raw('RAND()'))->first();
				if(count($data)>0) {
					$embed=$data->embed;
					$data=$data->toArray();
					$data['embed']=$embed;
					$data['is_default']=1;
					$data['ad_url'].='?from='.$from;
				}
			}
		}
		return Response::json(array(
			'result'=>count($data)>0,
			'data'=>$data?$data:array()
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE)->setCallback($request->get('callback'));
		;
	}

	public function index() {
		$datalist=array();
		if(Input::get('kind')>0) {
			$kind=Input::get('kind');
			$key='kind.'.$kind;
			$datalist=$this->getNewsByCategory(Input::get('kind'));
			$count=count($datalist);
			$this->insert_ad($datalist);
		}
		else
		if(Input::get('search')) {
			$datalist=Posts::latest()->where('posts.content','like','%'.Input::get('search').'%')->orWhere('posts.title','like','%'.Input::get('search').'%')->get();
		}
		return Response::j(count($datalist)>0,$datalist);
	}

	public function show360($idx,$id) {
		$v=DD360::getInstance()->get($id);
		$buff=array();
		$colorful="border:1px solid #CCC;border-left:5px solid #CCC";
		if($v['type']=='live') {
			$colorful="border:1px solid #CCC;border-left:5px solid #ff0000";
		}
		$buff[]='<div style="'.$colorful.';width:678px;padding:10px;margin-bottom:10px;">';
		$buff[]='<h3>'.$idx.'.</h3>';
		$buff[]=$v['embed'];
		$buff[]='<h3>'.$v['title'].'</h3>';
		$buff[]='<p>'.$v['description'].'</p>';
		$buff[]='<div>編號：'.$v['id'].'</div>';
		$buff[]='<div>類型：'.$v['type'].'</div>';
		$buff[]='<div>thumb：'.$v['image'].'</div>';
		$buff[]='</div>';
		return implode('',$buff);
	}

	public function channel360() {
		if(Input::get('debug')) {
			return Response::json([
			'result'=>true,
			'data'=>json_decode(DD360::getInstance()->live())],200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
		}

		if(Input::get('show')) {
			$data=DD360::getInstance()->api();
			$html='';
			$idx=1;
			foreach($data['videos'] as $video) {
				$html.=$this->show360($idx,$video['id']);
				$idx++;
			}
			foreach($data['streams'] as $video) {
				$html.=$this->show360($idx,$video['id']);
				$idx++;
			}

			return $html;
		}
		//$validDict=DD360::getInstance()->get();

		//create output
		$output=array(
			'streams'=> array(),
			'videos'=> array()
		);
		$dl=Videos::latest()->get();

		foreach($dl as $d) {
			if($d->type=='vod') {
				$output['videos'][]=$d;
				$d->status='';
			}
			else {
				//if(array_key_exists($d->source_code_youtube, $validDict)){
				//  $d->status='Streaming';
				//}else{
				//  $d->status='Stop';
				//}
				$output['streams'][]=$d->toArray();
			}
		}
		foreach($output['streams'] as &$s) {
			$s['Status']=$s['status'];
		}
		unset($s);
		if(Input::get('nofilter')) {
			dd($output);
		}
		return Response::json([
		'result'=>true,
		'data'=>$output],200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function channel24hours() {
		$datalist='https://www.youtube.com/watch?v=oTpt0GVKkPA';
		// $youtube_id='oTpt0GVKkPA';
		$youtube_id='Od-5LRHr-DQ';
		return Response::j(count($datalist)>0,$datalist,array('youtube_id'=>$youtube_id));
	}

	public function channelEvent() {
		$datalist=array();
		$datalist=NewEvent::latest()->get();
		return Response::json(array(
			'result'=>count($datalist)>0,
			'data'=>$datalist
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function channelStar() {
		$datalist=Star::latest()->get();
		return Response::json(array(
			'result'=>count($datalist)>0,
			'data'=>$datalist
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function channelCtiVideos() {
		$result=array(
			"result"=>true,
			"id"=>1,
			"name"=>"影音新聞",
			"sort"=>"1",
			"serveron"=>"Y",
			"hl"=>"",
			"image"=>"http://bepo.ctitv.com.tw/bepoapp/star_thumbs/ok01.jpg",
			"image_thum"=>"http://bepo.ctitv.com.tw/bepoapp/star_thumbs/ok01.jpg",
			//https://www.youtube.com/user/ctitvnews52/videos
			"htm_url"=>"https://www.youtube.com/channel/UCpu3bemTQwAU8PqM4kJdoEQ/videos",
			//https://www.youtube.com/user/ctitvnews52/videos
			"mobile_url"=>"https://www.youtube.com/channel/UCpu3bemTQwAU8PqM4kJdoEQ/videos"
		);
		return Response::json($result,200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function channelHotest() {
		$datalist=Hotest::latest()->get();
		return Response::json(array(
			'result'=>count($datalist)>0,
			'data'=>$datalist
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	public function embed($kind,$limit=10) {
		$limit=$limit>20?20:$limit;
		$datalist=$this->getNewsByCategory($kind,$limit);
		return view('embed',array('datalist'=>$datalist));
	}

	public function channelLive() {
		//$datalist=Live::latest()->get() ;
		$datalist=$this->getNewsByCategory(14)->take(10);
		$lives=array();
		foreach($datalist as $i=>$d) {
			$lives[]=array(
				'id'=>$d['id'],
				'name'=>$d['title'],
				'sort'=>$i,
				'serveron'=>'Y',
				'hl'=>"",
				'image'=>$d['image'],
				'image_thum'=>$d['image_thum'],
				'htm_url'=>$d['mobile_url'],
				'mobile_url'=>$d['mobile_url']
			);
		}
		return Response::json(array(
			'result'=>count($lives)>0,
			'data'=>$lives
		),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

	//@return false=>新版,true=舊版
	public function AppCheckVer() {
		$appid=Input::get('appid');
		$ver=Input::get('ver');
		$ver=str_replace('.','',$ver)*1;
		$flag=true;
		if($appid&&$ver) {
			$data=AppName::find($appid);
			if($data) {
				$curr=$data->version;
				$curr=str_replace('.','',$curr)*1;
				$flag=$ver>=$curr;
			}
		}
		return Response::json(array('ver'=>$flag,),200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
	}

}
