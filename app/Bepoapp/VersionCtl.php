<?php

namespace App\Bepoapp;

use Illuminate\Database\Eloquent\Model;

class VersionCtl extends Model {
	public $table='bepoapp_app_name';
    const CREATED_AT = 'publish_at';
	public $timestamps=false;
}
