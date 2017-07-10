<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use App\Helper\FCMHelper;

class FCMNotificationConsole extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature='bepo:fcmnotify';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description='bepo fcmnotify';

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
			if($secs_begin_at>$now) {
				continue;
			}

			//"title"=> "二戰以來最慘屠殺！  日安養院殺人兇嫌竟要政府實施「身障者安樂死」",
			//"mobile_url"=> "http://bepo.ctitv.com.tw/bepoapp/articles/20160726/13645",
			$push=array(
				'alert'=>$data->title,
				'url'=>$data->post->mobile_url
			);
			$begin_at=date('Y-m-d H:i:s');
			$code=FCMHelper::pushMessage($data->toArray());

			if($code['messageId']) {
				$data->status=2;
				$data->android_success=$code['android']['success'];
				$data->android_error=$code['android']['error'];
				$data->ios_success=$code['ios']['success'];
				$data->ios_error=$code['ios']['error'];
				$data->elapsed_time=$code['elapsed_time'];
				$data->begin_at=$begin_at;
				$data->end_at=date('Y-m-d H:i:s');
				$data->message_id=$code['messageId'];
				$data->save();
			}
			else {

			}
		}
	}

}
