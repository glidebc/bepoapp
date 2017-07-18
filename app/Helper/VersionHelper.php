<?php

namespace App\Helper;
use Log;

class VersionHelper {
	static function get($retArray=false) {
		$path=base_path().'/version.json';
		$obj=json_decode(file_get_contents($path));
		if($obj) {
			$number=array_shift($obj->numbers);
			if($retArray) {
				return $number;
			}
			return '版本 '.$number[0].' 日期 '.date('Y-m-d',strtotime($number[1])).' 註記 '.$number[2];
		}
		return '';
	}

	static function set($version,$date,$hash) {
		$path=base_path().'/version.json';
		$obj=json_decode(file_get_contents($path));
		if(!$obj) {
			Log::error("invalid file ".$path);
		}
		else {
			$obj->numbers=array(array($version,$date,$hash));
			$json=json_encode($obj);
			file_put_contents($path,$json);
		}
	}

}
