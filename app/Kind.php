<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;

class Kind extends Model {
	protected $primaryKey='kind_id';
	protected $connection='showtv';
	protected $table='kind';
	public $timestamps=false;
	public function scopeOptions() {
		return $this->where('active','=','1')->pluck('kind_name','kind_id')->toArray();
	}
}
