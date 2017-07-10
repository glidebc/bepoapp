<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveHits extends Model
{
	public function live() {
		return $this -> belongsTo('App\Live');
	}
}
