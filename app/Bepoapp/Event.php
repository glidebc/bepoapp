<?php

namespace App\Bepoapp;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model {
	public $table='bepoapp_event';
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
        return $this->orderBy('priority','asc')
            ->where('end_time','>',Carbon::now())
            ->where('start_time','<',Carbon::now())
            ->where('status','=','1')->take(200);
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
		return 'http://bepo.ctitv.com.tw/bepoapp/event_thumbs/'.$image;
    }
	public function getImageThumAttribute(){
		$image=$this->attributes['thumb_url'];
		if($image)
		return 'http://bepo.ctitv.com.tw/bepoapp/event_thumbs/'.$image;
    }
	public function getHtmUrlAttribute(){
		return $this->url;
    }
	public function getMobileUrlAttribute(){
		return $this->url;
    }
	
}
