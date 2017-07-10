<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model {
	protected $table='system';
	protected $fillable=array(
		'name',
		'path',
		'description',
		'priority',
		'enabled'
	);
}
