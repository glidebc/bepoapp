<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class FcmNotification extends Model {
	public $table='fcm_notification';
	public function device() {
		return $this->belongsTo('App\Model\FCMDeviceModel','fcm_id');
	}

	public function notification() {
		return $this->belongsTo('App\Notification','notification_id');
	}

}
