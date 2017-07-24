<?php

namespace App\Model\Sticker;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorModel extends Model {
	use SoftDeletes;
	protected $connection='mysql';
	protected $table="vendor";
	protected $attributes=array();
	protected $dates=array('deleted_at');
}
