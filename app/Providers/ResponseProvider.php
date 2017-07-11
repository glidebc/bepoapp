<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Response;
use Request;

class ResponseProvider extends ServiceProvider {
	public function boot() {
		Response::macro('j',function($result,$data,$extends=array()) {
			$prepare=array(
				'result'=>$result,
				'data'=>$data
			);
			foreach($extends as $k=>$v) {
				$prepare[$k]=$v;
			}
			$resp=Response::json($prepare,200,array('Content-type'=>'application/json; charset=utf-8'),JSON_UNESCAPED_UNICODE);
			if(Request::has('callback')) {
				$resp->setCallback(Request::get('callback'));
			}
			return $resp;
		});
	}

	public function register() {
	}

}
