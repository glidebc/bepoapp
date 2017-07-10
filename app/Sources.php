<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sources extends Model
{
    public function scopeOptions(){
        return $this->orderBy('name', 'asc') -> pluck('name', 'id')->toArray();
    }

}
