<?php

namespace App\Model\CTI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;

class ProgramCalendar extends Model {
    protected $table='cti_program_calendar';
    protected $visible=array();
    protected $appends=array();
    protected $guarded=array();

}
