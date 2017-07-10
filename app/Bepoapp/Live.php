<?php

namespace App\Bepoapp;

use Illuminate\Database\Eloquent\Model;

class Live extends Model {
	public $table='bepoapp_live';
    protected $visible=array(
        'id',
        'name',
        'sort',
        'serveron',
        'hl',
        'image',
        'image_thum',
		'htm_url',
		'mobile_url',
    );
	protected $appends=array(
		'name',
		'sort',
        'serveron',
        'hl',
        'image',
        'image_thum',
		'htm_url',
		'mobile_url',
    );
	public function scopeLatest(){
        return $this->orderBy('priority','asc')->take(200);
    }
	public function getServeronAttribute(){
		return 'Y';
	}
	public function getHlAttribute(){
		return '';
	}
	public function getSortAttribute(){
		return $this->attributes['priority'];
	}
	public function getNameAttribute(){
		return $this->attributes['title'];
	}
	public function getImageAttribute(){
		$image=$this->attributes['thumb_url'];
		if($image)
		return 'http://bepo.ctitv.com.tw/bepoapp/live_thumbs/'.$image;
    }
	public function getImageThumAttribute(){
		$image=$this->attributes['thumb_url'];
		if($image)
		return 'http://bepo.ctitv.com.tw/bepoapp/live_thumbs/'.$image;
    }
	public function getHtmUrlAttribute(){
		return $this->url;
    }
	public function getMobileUrlAttribute(){
		return $this->url;
    }
}
