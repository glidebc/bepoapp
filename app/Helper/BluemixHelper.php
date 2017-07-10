<?php
#ref https://mobile.ng.bluemix.net/imfpushrestapidocs/#!/messages/post_apps_applicationId_messages
#curl -X POST --header 'Content-Type: application/json' --header 'Accept: application/json' --header 'appSecret: 01c731f9-9784-4a8a-8f07-2f6fe5ef662f' --header 'Accept-Language: en-US' -d '{"message":{"alert":"test"}}' 'https://mobile.ng.bluemix.net/imfpush/v1/apps/5c4252b7-da17-47ef-bfb2-2ed282daa09f/messages'
namespace App\Helper;
use Log;

class BlueMixBase {
	protected $ch=null;
	#protected $appId='d200f279-6cb2-4bec-890b-e92f7a277083';
	protected $appId='c71fb4fc-a853-4624-9405-d4cded11f4be';
	protected $url;
	protected $error;
	protected $baseurl='https://mobile.ng.bluemix.net/imfpush/v1/apps/';
	protected $headers=array(
		'Content-Type: application/json',
		'Accept: application/json',
		#'appSecret: 01c731f9-9784-4a8a-8f07-2f6fe5ef662f'
		'appSecret: c9633ab1-bd54-45f7-ae9f-9ebe18eba6e3'
	);
	function __construct() {
		$this->initialize();
	}

	public function url($url) {
		$this->url=$this->baseurl.$this->appId.'/'.$url;
		return $this;
	}

	public function initialize() {
		$this->ch=curl_init();
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->headers);
	}

	public function get($data=array()) {
		curl_setopt($this->ch,CURLOPT_POST,0);
		curl_setopt($this->ch,CURLOPT_URL,$this->url);
		curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($data,JSON_UNESCAPED_UNICODE));
		$output=curl_exec($this->ch);
		$this->error=curl_error($this->ch);
        $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
		return json_decode($output,true);
	}

	public function post($data) {
		curl_setopt($this->ch,CURLOPT_POST,1);
		curl_setopt($this->ch,CURLOPT_URL,$this->url);
		curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($data,JSON_UNESCAPED_UNICODE));
		$output=curl_exec($this->ch);
		$this->error=curl_error($this->ch);
		return json_decode($output,true);
	}

}

class BluemixHelper extends BlueMixBase {

	static public function getSettings() {
		$bx=new BlueMixBase();
		return $bx->url('settings')->get();
	}

	static public function pushMessage($message) {
		$bx=new BlueMixBase();
		$result=$bx->url('messages')->post($message);
		$logFile = 'bluemix.log';
		Log::useDailyFiles(storage_path().'/logs/'.$logFile);
		Log::info('push message '.json_encode($message,JSON_UNESCAPED_UNICODE).' return:'
			.json_encode($result,JSON_UNESCAPED_UNICODE));
		return $result;
	}

	static public function getDevices($message) {
		$bx=new BlueMixBase();
	}

}

//$message=array('message'=> array('alert'=>'hello,world'));
//$ret=$bx->pushMesssage($message);
