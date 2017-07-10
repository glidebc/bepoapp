<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
	public $table='notification';
	public function post() {
		return $this->belongsTo('App\Posts');
	}

	public function scopeValid() {
		return $this->where('status','=','0');
	}
	public function scopeEnabled() {
		return $this->where('status','=','2')->orderBy('play_at','desc')
			->where('message_id','!=','')
			->orderBy('id','desc');
	}

}
