<?php

namespace App\Bepoapp;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model {
	public $table='bepoapp_categories';
    protected $visible=array(
        'id',
        'name',
        'sort',
        'serveron',
        'hl',
        'active'
    );
	protected $appends=array(
		'name',
		'sort',
        'serveron',
        'hl',
        'active'
    );
    public function scopeOptions() {
        return $this->orderBy('title','asc')->pluck('title','id')->toArray();
    }
	public function getServeronAttribute(){
		return  $this->attributes['enabled']?'Y':'N';
	}
	public function getHlAttribute(){
		return $this->attributes['highlight'];
	}
	public function getActiveAttribute(){
		return $this->attributes['active'];
	}
	public function getSortAttribute(){
		return $this->attributes['priority'];
	}
	public function getNameAttribute(){
		return $this->attributes['title'];
	}

}
