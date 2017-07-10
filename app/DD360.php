<?php

namespace App;
use DateHelper;

class DD360 {
	private $ch = null;
	private $timeout = 60;
	private $headers = array();
	private $verbose = false;
	private static $ins=null;
	private $contents=array();
	function __construct() {
		$this -> ch = curl_init();
		curl_setopt($this -> ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this -> ch, CURLOPT_TIMEOUT, $this -> timeout);
		curl_setopt($this -> ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this -> ch, CURLOPT_VERBOSE, $this -> verbose);
		$this->init();
	}
	
	private function init(){
		$baseFilePath = 'https://deo4bapflag21.cloudfront.net/';
		$video_output = array();
		
		//Vidoes
		$videos=json_decode($this->videos(),true);
		if(is_array($videos['videos'])){
			foreach ($videos['videos'] as $video) {
				$video['filepath'] = $baseFilePath . $video['filepath'];
				$video['screenshot']['filepath'] = $baseFilePath . $video['screenshot']['filepath'];
				$this->contents[$video['hash']] = array(
					'id' => $video['hash'],
					'title' => $video['title'],
					'pubdate' => DateHelper::zhDateTime($video['time_uploaded']),
					'image' => $video['screenshot']['filepath'],
					'mobile_url' => $video['filepath'],
					'description' => $video['description'],
					'embed'=>'<iframe src="https://www.im360.com/embed/'.$video['hash'].'" frameborder="0" allowfullscreen=""></iframe>',
					'adid' => '',
					'type'=>'vod'
				);
			}
		}
		
		//Streams
		$streams=json_decode($this->live(),true);
		if(is_array($streams['streams'] )){
			foreach ($streams['streams'] as $stream) {
				$this->contents[$stream['hash']] = array(
					'id' => $stream['hash'],
					'title' => $stream['name'],
					'pubdate' => DateHelper::zhDateTime($stream['start_time']),
					'image' => $baseFilePath . $stream['screenshot'],
					'mobile_url' => $stream['origin'],
					'description' => $stream['description'],
					'status' => $stream['status'],
					'embed'=>'<iframe src="http://www.im360.com/embed/live/'.$stream['hash'].'" frameborder="0" allowfullscreen=""></iframe>',
					'adid' => '',
					'type'=>'live'
				);
			}
		}
	}

	function setHeader($k, $v) {
		$this -> headers[$k] = $v;
		return $this;
	}

	function setHeaders(Array $headers) {
		foreach ($headers as $k => $v)
			$this -> setHeader($k, $v);
		return $this;
	}
	public function api(){
		$v_list=array();
		$l_list=array();
		foreach( $this->contents as $d ) {
			if($d['type']=='vod'){
				$v_list[]=array(
				    'id'=>$d['id'],
					'title'=>$d['title'],
					'pubdate'=>$d['pubdate'],
					'image'=>$d['image'],
					'mobile_url'=>$d['mobile_url'],
					'adid'=>$d['adid']
				);	
			}
			else if($d['type']=='live'){
				$l_list[]=array(
					'id'=>$d['id'],
					'title'=>$d['title'],
					'pubdate'=>$d['pubdate'],
					'image'=>$d['image'],
					'mobile_url'=>$d['mobile_url'],
					'status'=>$d['status'],
					'adid'=>$d['adid']
				);	
			}
			else{
				
			}
		}
		return array('streams'=>$l_list,'videos'=>$v_list);
	}
	public function get($hash=null){
		if($hash==null)return $this->contents;
		if(array_key_exists($hash,$this->contents)){
			return $this->contents[$hash];
		}else{
			return null;
		}
	}
	public static function getInstance() {
		if(self::$ins==null){
			self::$ins=new DD360();
		}
		return self::$ins;
	}

	function close() {
		curl_close($this -> ch);
	}

	function httpget($url) {
		$headers = array();
		foreach ($this->headers as $k => $v) {
			$headers[] = "$k: $v";
		}
		curl_setopt($this -> ch, CURLOPT_HTTPHEADER, $headers);
		$error = curl_error($this -> ch);
		if ($error) {
			log_message('error','httpget url:'.$url.' erropr'.$error);
			return '';
		}
		curl_setopt($this -> ch, CURLOPT_URL, $url);
		$this -> headers = array();
		return curl_exec($this -> ch);
	}

	function live() {
		$headers = array(
			'Content-Type' => 'application/json',
			'Content-Length' => '0',
			'Protocol-Version' => '1.0',
			'Key' => '5883fc48-0a4b-4e2f-9477-210ce844dc9c',
			'Signature' => 'RLJ0bZng+cGTGhl3CsIq+z2JF9m94MMupY2cdOU2HxBlAh66OB6y0Hg2EZIfYQKfoZyLWxa/Cz4CEQ6Kqxjcgg=='
		);
		$url = 'https://api.im360.com/api/v1.0/channel/e66fa5d3-fbd9-4d08-bd36-b3c904865204/live';
		return $this -> setHeaders($headers) -> httpget($url);
	}

	function videos() {
		$headers = array(
			'Content-Type' => 'application/json',
			'Content-Length' => '0',
			'Protocol-Version' => '1.0',
			'Key' => '5883fc48-0a4b-4e2f-9477-210ce844dc9c',
			'Signature' => '+xUM38bQADWFU2wc2QEb8HbOFxUIT/9nhJaxTkYFu7VQL+H929UxOaM4RRhPa3HPQ8ivRUwLJm4IwIBYw8MufA=='
		);
		$url = 'https://api.im360.com/api/v1.0/channel/e66fa5d3-fbd9-4d08-bd36-b3c904865204/videos/private?offset=0&limit=20';
		return $this -> setHeaders($headers) -> httpget($url);
	}

}
