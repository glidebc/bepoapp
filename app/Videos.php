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

class Videos extends Model {
	protected $connection = 'showtv';
    protected $primaryKey = 'auto';
	protected $table = 'video';
	const CREATED_AT = 'creat_datetime';
	const UPDATED_AT = 'edit_datetime';
}
