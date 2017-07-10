<?php

namespace App\Model\NewBepoTV;
use Jenssegers\Mongodb\Eloquent\Model as baseEloquent;
use Auth;
use DateTime;

class Model extends baseEloquent {
	protected $embeds=array();
	public function save(array $options=[]) {
		if($this->isDirty()) {

		}
		//add create/update user id
		if(Auth::user()) {
			$aModel=\App\User::find(Auth::user()->id);
			if(count($aModel)) {
				$array=$aModel->toArray();
				$this->attributes['created']=Auth::user()->id;
				$this->attributes['updated']=Auth::user()->id;
			}
		}
		return $saved=parent::save();
	}

	public function getAttribute($key) {
		if(array_key_exists($key,$this->embeds)) {
			$b=array();
			$r=array_get($this->attributes,$key);
			if(is_array($r)) {
				foreach($r as $v) {
					$b[]=@(string)$v['_id'];
				}
			}
			return @implode(',',$b);
		}
		else {
			$ret=parent::getAttribute($key);
			return is_null($ret)?'':$ret;
		}
	}

	public function scopeOfDateTime($query,$fldname,$value) {
		$paris=explode('|',$value);
		if($paris[0])
			$query=$query->where($fldname,'>=',new DateTime($paris[0]));
		if($paris[1])
			$query=$query->where($fldname,'<=',new DateTime($paris[1].' 23:59:59.999999'));
		return $query;
	}

	public function scopeOfCreateAt($query,$value) {
		return $this->scopeOfDateTime($query,'created_at',$value);
	}

	public function scopeOfDeleteAt($query,$value) {
		return $this->scopeOfDateTime($query,'deleted_at',$value);
	}

	public function scopeOfUpdatedAt($query,$value) {
		return $this->scopeOfDateTime($query,'updated_at',$value);
	}

	public function setAttribute($key,$value) {
		if(array_key_exists($key,$this->embeds)) {
			$this->attributes[$key]=array();
			$class=$this->embeds[$key];
			$b=array();
			foreach(explode(',',$value) as $v) {
				$v=trim($v);
				$c=call_user_func($class.'::find',$v);
				if(count($c)) {
					$c->touch();
					$array=$c->toArray();
					$ok=array_filter($array,function($key) {
						return in_array($key,array(
							'name',
							'title'
						));
					},ARRAY_FILTER_USE_KEY);
					$ok['_id']=$t=new \MongoDB\BSON\ObjectID($v);
					ksort($ok);
					$b[]=$ok;
				}
			}
			$this->attributes[$key]=$b;
		}
		else {
			if(is_string($value)) {
				$value=trim($value);
			}
			if(in_array($key,array(
				'status',
				'sort',
				'active',
				'views'
			))) {
				$value=$value*1;
			}
			return parent::setAttribute($key,$value);
		}
	}

}
