<?php

namespace App\Model\Pet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;


class CogiRun extends Model {
    protected $connection='pets';
    protected $table='cogi_run';
    public $timestamps=false;
    protected $visible=array();
    protected $appends=array();
    protected $guarded=array();
    public function getPicPathAttribute(){
	return 'http://events.ctitv.com.tw/2016petsleague/'.$this->attributes['pic_path'];
    }
}
