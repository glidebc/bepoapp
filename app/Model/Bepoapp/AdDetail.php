<?php

namespace App\Model\Bepoapp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;
use Cviebrock\EloquentTaggable\Taggable;

//{"result":true,"data":{"adid":"1471233914","type":"image","click_url":"http://tw.yahoo.com","ad_url":"http://www.ctitv.com.tw/ctiapp/bannerhtm/1471233914.htm"}}

class AdDetail extends Model {
	protected $table='bepoapp_ad_detail';
	protected $appends=array('delta');
	public function getdeltaAttribute() {
		if($this->show>0&&$this->click>0) {
			return sprintf('%.02f',$this->click/$this->show*100).'%';
		}
		return '';
	}

}
