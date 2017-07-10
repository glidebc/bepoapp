<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationHits extends Model
{
	public function notification() {
		return $this -> belongsTo('App\Notification');
	}
}
