<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Uuid;
use Input;
use GuzzleHttp\Client;
use App\Model\CheckerModel;

class CheckerController extends Controller {
	public function run() {
		$client=new Client();
		$urls=array(
			array(
				'url'=>'http://google.com',
				'method'=>'get',
				'params'=> array()
			),
			array(
				'url'=>'http://google.com',
				'method'=>'get',
				'params'=> array()
			),
			array(
				'url'=>'http://google.com',
				'method'=>'get',
				'params'=> array()
			),
			array(
				'url'=>'http://google.com',
				'method'=>'get',
				'params'=> array()
			)
		);
		foreach($urls as $url) {
			$res=$client->request($url['method'],$url['url'],$url['params']);
			$code=$res->getStatusCode();
			$length=strlen($res->getBody());
			//echo json_encode($url),',',$code,',',$length;
		}
	}

	public function anyIndex(Request $request,$path=null) {
		if($request->has('do')) {
			$ids=$request->input('ids');
			if($ids) {
				$coll=CheckerModel::whereIn('id',array_keys($ids))->delete();
			}
		}
		$data=array('datalist'=>CheckerModel::all());
		return view('checker.index',$data);
	}

}
