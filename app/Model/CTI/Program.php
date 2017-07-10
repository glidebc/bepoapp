<?php

namespace App\Model\CTI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use DateHelper;
use Carbon\Carbon;

class Program extends Model {
	protected $table='cti_program';
	protected $visible=array();
	protected $appends=array();
	protected $guarded=array();

}
