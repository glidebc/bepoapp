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

class Shopping_Posts extends Model {
	protected $primaryKey = 'ID';
	protected $connection = 'platform';
	protected $table='shopping_posts';
	public $timestamps=false;
}
