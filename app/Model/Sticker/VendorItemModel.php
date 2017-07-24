<?php

namespace App\Model\Sticker;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorItemModel extends Model {
	use SoftDeletes;
	protected $connection='mysql';
	protected $table="vendor_item";
	protected $attributes=array();
	protected $dates=array('deleted_at');
}
