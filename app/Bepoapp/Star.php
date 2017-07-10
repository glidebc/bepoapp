<?php

namespace App\Bepoapp;

use Illuminate\Database\Eloquent\Model;

class Star extends Model {
	public $table='bepoapp_star';
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
        return $this->orderBy('priority','asc')->take(200)->where('status','=','1');
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
		return 'http://bepo.ctitv.com.tw/bepoapp/star_thumbs/'.$image;
    }
	public function getImageThumAttribute(){
		$image=$this->attributes['thumb_url'];
		if($image)
		return 'http://bepo.ctitv.com.tw/bepoapp/star_thumbs/'.$image;
    }
	public function getHtmUrlAttribute(){
		return $this->url;
    }
	public function getMobileUrlAttribute(){
		return $this->url;
    }
}
