<?php

namespace App\Helper;

class OptHelper {
	//Disable it
	static $cache=array();
	private static function set($k,$v){
		self::$cache($k,$v);
	}
	private static function get($k){
		if(array_key_exists($k,self::$cache))return self::$cache[$key];
		return false;
	}

	static function getCategoryName($key) {
		foreach(self::getCategory(true) as $k=>$v) {
			if(0==strcmp($key,$k))
				return $v;
		}
		return '';
	}

	static function getProgramName($key) {
		foreach(self::getProgram(true) as $k=>$v) {
			if(0==strcmp($key,$k))
				return $v;
		}
		return '';
	}

	static function getZoneName($key) {
		foreach(self::getZone(true) as $k=>$v) {
			if(0==strcmp($key,$k))
				return $v;
		}
		return '';
	}

	static function getCategory($all=false) {
		$opts=\App\Model\NewBepoTV\CategoryModel::orderBy('sort','asc')->get();
		$temp=array();
		foreach($opts as $data) {
			$id=$data->_id;
			if($all){
				$temp[$id]=$data->name;
			}else{
				if($data->status)$temp[$id]=$data->name;
			}
		}
		return $temp;
	}

	static function getProgram($all=false) {
		$opts=\App\Model\NewBepoTV\ProgramModel::orderBy('sort','asc')->get();
		$temp=array();
		foreach($opts as $data) {
			$id=$data->_id;
			if($all){
				$temp[$id]=$data->name;
			}else{
				if($data->status)$temp[$id]=$data->name;
			}
		}
		return $temp;
	}

	static function getZone($all=false) {
		$opts=\App\Model\NewBepoTV\ZoneModel::get();
		$temp=array();
		foreach($opts as $data) {
			$id=$data->_id;
			if($all){
				$temp[$id]=$data->name;
			}else{
				if($data->status)$temp[$id]=$data->name;
			}
		}
		return $temp;
	}

}
