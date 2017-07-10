<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carousel extends Model
{
	public $table='carousel';
    public function post(){
        return $this->belongsTo('App\Post');
    }
}
