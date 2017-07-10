<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use BluemixHelper;
use Log;

class Version extends Command {
	protected $signature='version';
	protected $description='version';
	public function __construct() {
		parent::__construct();
	}

	public function handle() {
		$cmd='git log --pretty=format:"%H%x09%an%x09%ad%x09%s" --date=short';
		exec($cmd,$lines,$ret);
		if($ret==0) {
			/*
			 Array
			 (
			 [0] => 7451b5b9e76d4b91029b0e515eaaf22565b84c72
			 [1] => shan
			 [2] => 2016-03-22
			 [3] => update acacha/admin-lte-template-laravel
			 )
			 */
			$i=0;
			/*
			 Array
			 (
			 [0] => 1.0.0
			 [1] => 20170101
			 [2] => 9c2d347b94c35a555730da7a42b68023d6293db9
			 )
			 */
			$current=\App\Helper\VersionHelper::get(true);
			$found=false;
			foreach($lines as $l) {
				$i++;
				$data=preg_split("/\s+/",$l,4);
				if($current[2]==trim($data[0])) {
					$found=true;
					break;
				}
			}
			if($found) {
				if($i==1) {
					$this->info($current[0].' '.$current[1].' '.$current[2]);
				}
				else {
					$new=preg_split("/\s+/",$lines[0]);
					$n=$current[0];
					$buff=array(
						$this->add($n,$i-1),
						$new[2],
						$new[0]
					);
					$this->info($buff[0].' '.$buff[1].' '.$buff[2]);
					\App\Helper\VersionHelper::set($buff);
				}
			}
			else {
				Log::error("make version increment error.");
				$this->error("error:version fail.");
			}
		}
	}

	public function add($n,$i) {
		$data=explode('.',$n,3);
		$v=$data[0]*10000+$data[1]*100+$data[2];
		$v+=$i;
		return floor($v/10000).'.'.floor(($v%10000)/100).'.'.$v%100;
	}

}
