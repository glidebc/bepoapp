<?php

namespace App\Ddbepo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;

class Video_Class extends Model {
	protected $primaryKey = 'vc_id';
	protected $connection = 'ddbepo';
	protected $table='video_class';
	public $timestamps=false;
	public function scopeOptions() {
        return $this->orderBy('sort','asc')->pluck('vc_name','vc_id')->toArray();
    }
}
