<?php

namespace App\Model\Showbar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use DateHelper;
use Carbon\Carbon;

class Video_Class extends Model {
    protected $connection='showbar';
    protected $primaryKey='vc_id';
    protected $table='video_class';
    public $timestamps=false;
    public function scopeOptions() {
        return $this->orderBy('sort','asc')->where('active','1')
            ->where('p_id','pg20160202175139')
            ->pluck('vc_name','vc_id')->toArray();
    }

}
