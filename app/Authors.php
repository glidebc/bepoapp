<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Authors extends Model
{
    protected $fillable = [
        'name','path','description','priority','enabled'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
	public function scopeOptions(){
        return $this->orderBy('name', 'asc') -> pluck('name', 'id')->toArray();
    }
}
