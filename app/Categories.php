<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model {
    protected $visible=array(
        'id',
        'name',
        'sort',
        'serveron',
        'hl'
    );
	protected $appends=array(
		'name',
		'sort',
        'serveron',
        'hl'
    );
    public function scopeOptions() {
        return $this->orderBy('title','asc')->pluck('title','id')->toArray();
    }
	public function getServeronAttribute(){
		return 'Y';
	}
	public function getHlAttribute(){
		return $this->attributes['highlight'];
	}
	public function getSortAttribute(){
		return $this->attributes['priority'];
	}
	public function getNameAttribute(){
		return $this->attributes['title'];
	}

}
