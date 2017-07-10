<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use BluemixHelper;

class NotificationConsole extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature='bepo:notify';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description='bepo notify';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */

	public function handle() {
		$result=Notification::valid()->get();
		foreach($result as $data) {
			
			$secs_begin_at=strtotime($data->play_at);
			$now=time();
			
			//尚未到推播時間
			if($secs_begin_at>$now){
				continue;
			}
            
            //push queue
            
            
			
			//"title"=> "二戰以來最慘屠殺！  日安養院殺人兇嫌竟要政府實施「身障者安樂死」",
			//"mobile_url"=> "http://bepo.ctitv.com.tw/bepoapp/articles/20160726/13645",
			$push=array(
				'alert'=>$data->title,
				'url'=>$data->post->mobile_url
			);
			$begin_at=date('Y-m-d H:i:s');
			$code=BluemixHelper::pushMessage(array('message'=>$push));
			if($code['messageId']) {
				$data->status=2;
				$data->begin_at=$begin_at;
				$data->end_at=date('Y-m-d H:i:s');
				$data->message_id=$code['messageId'];
				$data->save();
			} else {
				
			}
		}
	}
}
