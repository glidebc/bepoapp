<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Posts;
use Response;
use Input;
use DateHelper;
use DB;
use Log;

class ArticlesController extends Controller {
	public function show($date,$id,$slug='') {
		$path='bepoapp.article.'.$id;
		$type=Input::get('type');
		$pModel=Posts::where('id','=',$id)->where(DB::raw('date(post_at)'),'=',$date)->select('*')->firstOrFail();
		if($type=='json') {
			$d=$pModel->toArray();
			$d['content']=$pModel->content;
			return json_encode($d,JSON_UNESCAPED_UNICODE);
		}
		else {
			return view('mobile.article',array('data'=>$pModel));
		}
	}

	function get_client_ip() {
		$ipaddress='';
		if(getenv('HTTP_CLIENT_IP'))
			$ipaddress=getenv('HTTP_CLIENT_IP');
		else
		if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress=getenv('HTTP_X_FORWARDED_FOR');
		else
		if(getenv('HTTP_X_FORWARDED'))
			$ipaddress=getenv('HTTP_X_FORWARDED');
		else
		if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress=getenv('HTTP_FORWARDED_FOR');
		else
		if(getenv('HTTP_FORWARDED'))
			$ipaddress=getenv('HTTP_FORWARDED');
		else
		if(getenv('REMOTE_ADDR'))
			$ipaddress=getenv('REMOTE_ADDR');
		else
			$ipaddress='UNKNOWN';
		return $ipaddress;
	}

}
