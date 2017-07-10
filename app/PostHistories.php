<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostHistories extends Model {
    public $table='post_histories';
    public function author() {
        return $this->belongsTo('App\Authors');
    }

    public function post() {
        return $this->belongsTo('App\Posts');
    }

}
