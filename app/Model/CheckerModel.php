<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckerModel extends Model {
	use SoftDeletes;
	protected $table='checker';
	protected $dates=['deleted_at'];
}
