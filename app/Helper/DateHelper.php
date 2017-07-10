<?php

namespace App\Helper;

use Carbon\Carbon;

class DateHelper {
	public static function friendlyDateTime(){
		$t = time()- strtotime($value);
		$f = array(
			'31536000' => '年',
			'2592000' => '個月',
			'604800' => '星期',
			'86400' => '天',
			'3600' => '小時',
			'60' => '分鐘',
			'1' => '秒'
		);
		foreach($f as $k => $v){
			if(0 != $c = floor($t/(int)$k)){
				return $c.$v.'前';
			}
		}
		return '剛剛';
	}
	public static function zhDateTime($value) {
		$parsed = Carbon::parse($value);
		return sprintf("%04d/%02d/%02d %s %02d:%02d:%02d", $parsed -> year, $parsed -> month, $parsed -> day, $parsed -> hour > 12 ? '下午' : '上午', $parsed -> hour > 12 ? $parsed -> hour - 12 : $parsed -> hour, $parsed -> minute, $parsed -> second);
	}
}
