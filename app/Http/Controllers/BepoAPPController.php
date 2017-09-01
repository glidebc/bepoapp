<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Uuid;
use Input;

class BepoAPPController extends Controller {

	function __construct() {
		parent::__construct();
		$this->cfgFile=config_path('bepoapp.php');
	}

	private function cfgWriter($key,$params) {
		$insert=array();
		$keys=array_get($params,'keys',array());
		$values=array_get($params,'values',array());
		if($params['is_array']) {
			foreach($keys as $i=>$k) {
				if(strlen($values[$i])) {
					$insert[$k]=$values[$i];
				}
			}
		}
		else {
			$insert=@$values[0];
		}
		//writer
		$temp=$this->getCfg('global.php');
		$temp[$key]=$insert;
		$writer=array();
		foreach($temp as $k=>$v) {
			$k=str_replace(':','_',$k);
			array_set($writer,$k,$v);
		}
		$len=$this->write($writer);
	}

	private function write($writer) {
		$s=var_export($writer,true);
		$s="<?php return ".$s.";\n";
		file_put_contents($this->cfgFile,$s);
		return strlen($s);
	}

	private function getCfg() {
		$content=file_get_contents($this->cfgFile);
		$code=str_replace('<?php','',$content);
		$ret=array();
		$code='{'.$code.'};';
		$ret=eval("$code");
		return $ret;
	}

	public function getIndex(Request $request,$path=null) {
        $messages=array();
		if($request->has('do')) {
			$this->cfgWriter($request->input('key'),$request->input());
            $messages[]='更新完成!';
		}
		$temp=$this->getCfg();
		$temp=array_dot($temp);
		ksort($temp);
		$cfgs=array();
		foreach($temp as $k=>$v) {
			$paris=explode('.',$k);
			$n=count($paris);
			if($n>1) {
				$p=implode('.',array_slice($paris,0,$n-1));
				$key=$paris[$n-1];
				$cfgs[$p][$key]=$v;
			}
			else {
				$cfgs[$k]=$v;
			}
		}
		$temp1=array();
		foreach($cfgs as $k=>$v) {
			$buff=str_replace('_',':',$k);
			$temp1[$buff]=$v;
		}
       
		$data=@$temp1[$path];
		return view('config.index',array(
			'cfgs'=>$temp1,
			'data'=>$data,
			'messages'=>$messages
		));
	}

}
