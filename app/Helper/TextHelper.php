<?php

namespace App\Helper;

class TextHelper {
	static function  text_replace($string,$replacement,$start,$length=null){
    	$tmp=mb_substr($string,$start,$length);
	    if($string!==$tmp) {
	        $string = $tmp.$replacement;
	    }
	    return $string;
	}
}
