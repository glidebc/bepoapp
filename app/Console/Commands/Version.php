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
		/*
		 Array
		 (
		 [0] => 7451b5b9e76d4b91029b0e515eaaf22565b84c72
		 [1] => shan
		 [2] => 2016-03-22
		 [3] => update acacha/admin-lte-template-laravel
		 )
		 */
		if(count($lines)>0){
			$n=count($lines);
			$last=preg_split('/\s+/',$lines[0],4);
			$version=$this->getVersion($n);
			$date=$last[2];
			$hash=$last[0];
			\App\Helper\VersionHelper::set($version,$date,$hash);
			$this->info($version.' '.$date.' '.$hash);
		}else{
			Log::error("make version increment error.");
			$this->error("error:can't calc version");
		}
	}

	public function getVersion($n) {
		return floor($n/10000).'.'.floor(($n%10000)/100).'.'.$n%100;
	}

}
