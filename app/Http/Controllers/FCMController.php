<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Response;
use Input;
use Carbon\Carbon;
use DB;
use Cache;
use Log;
use App\Model\FCMDeviceModel;

class FCMController extends Controller {
	public function update(Request $request) {
		/*
		$logFile='fcm.log';
		Log::useDailyFiles(storage_path().'/logs/'.$logFile);
		Log::info(json_encode($request->all(),JSON_UNESCAPED_UNICODE));
		*/
		$resp=array(
			'code'=>0,
			'type'=>0,
			'message'=>'empty'
		);
		if($request->has('id')&&$request->has('token')&&$request->has('device')) {
			//{"id":"DEVICE_ID","token":"REG_ID","appversion":"APP_VERSION[ex:1.0.0"}
			//add field:device [ios,android]
			$type=2;
			if($request->input('device')=='ios')
				$type=0;
			else
			if($request->input('device')=='android')
				$type=1;

			$model=FCMDeviceModel::where('device_id','=',$request->input('id'))->where('type','=',$type)->first();
			if(is_null($model)||0==count($model))
				$model=new FCMDeviceModel();
			$model->device_id=$request->input('id');
			$model->token=$request->input('token');
			$model->extra=$request->input('appversion');
			$model->type=$type;
			$resp['code']=1;
			$resp['message']='success to get '.$request->input('device').' data';
			$model->save();
		}
		return $resp;
	}

}
