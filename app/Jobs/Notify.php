<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\FcmNotification;
use Log;
use Carbon\Carbon;
use App\Helper\FCMHelper;
class Notify extends Job implements ShouldQueue {
    use InteractsWithQueue, SerializesModels;
	protected $noti;
	public function __construct(FcmNotification $noti) {
		$this->noti=$noti;
	}

	public function handle() {
		return FCMHelper::send($this->noti);
	}

}
