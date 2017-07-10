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
use App\Model\Bepoapp\AdDetail;

//{"result":true,"data":{"adid":"1471233914","type":"image","click_url":"http://tw.yahoo.com","ad_url":"http://www.ctitv.com.tw/ctiapp/bannerhtm/1471233914.htm"}}

class Ad extends Model {
	use SoftDeletes;
	use Taggable;
	protected $table='bepoapp_ad';
	protected $visible=array(
		'adid',
		'click_url',
		'type',
		'ad_url',
		'youtube_id'
	);
	protected $appends=array(
		'adid',
		'ad_url',
		'delta',
		'daterange',
		'days',
		'views',
		'is_start'
	);
	protected $guarded=array(
		'deleted_at',
		'post_at'
	);
	protected $dates=['deleted_at'];
	public function getAdidAttribute() {
		return $this->id;
	}

	public function getIsStartAttribute() {
		$s=strtotime($this->begin_at);
		$e=strtotime($this->end_at);
		$now=time();
		if(($s<$now)&&($e>$now)&&$this->status==1) {
			return true;
		}
		else {
			return false;
		}
	}

	public function getAdUrlAttribute() {
		return url('adshow',$this->id);
	}

    public function getShowAttribute(){
        $total=0;
            foreach(AdDetail::where('ad_id','=',$this->id)->orderBy('date','asc')->get() as $data) {
                $total+=$data->show;
            }
            return $total;
    }
    public function getClickAttribute(){
        $total=0;
            foreach(AdDetail::where('ad_id','=',$this->id)->orderBy('date','asc')->get() as $data) {
                $total+=$data->click;
            }
            return $total;
    }


	public function getDaysAttribute() {
		$s=strtotime($this->begin_at);
		$e=strtotime($this->end_at);
		return ceil(($e-$s)/(24*60*60));
	}

    public function scopeBeginat($query,$value) {
        $d=explode('|',$value);
        if($d && count($d)>1){
            $s=$d[0];
            $e=$d[1];
            if($s && $e ){

                return $query=$query->whereRaw("date(begin_at) >= date('$s')")
                    ->whereRaw("date(begin_at) <= date('$e')");
            }
        }
        return $query;
    }
    public function scopeEndat($query,$value) {
        $d=explode('|',$value);
        if($d && count($d)>1){
            $s=$d[0];
            $e=$d[1];
            if($s && $e ){
                return $query->whereRaw("date(end_at) >= date('$s')")
                    ->whereRaw("date(end_at) <= date('$e')");
            }
        }
        return $query;
    }
	/*
	 *
	 <option value="0">放送中</option>
	 * <option value="1">已完成</option>
	 * <option value="2">已暫停</option>
	 * <option value="3" selected="selected">就緒</option></select>
	 *
	 */
	public function scopeStatus($query,$value) {
		switch($value) {
			case "0":
				$query=$query->whereRaw(DB::raw("end_at >= now()"))->where('status','=','1')
                    ->whereRaw(DB::raw("begin_at <= now()"));
				break;
			case "1":
				$query=$query->whereRaw(DB::raw("end_at <= now()"))->where('status','=','1')->whereRaw(DB::raw("begin_at <= now()"));
				break;
			case "2":
				$query=$query->where('status','=','0');
				break;
			case "3":
				$query=$query->whereRaw(DB::raw("begin_at >= now()"))->where('status','=','1')->whereRaw(DB::raw("end_at >= now()"));
				break;
			default:
		}
		return $query;
	}
	public function scopeCustomSelect($query) {
		return $query->select(DB::raw('*,case
                when status=0 then 2
                when begin_at >= now() and status=1 then 3
                when end_at >= now() and status=1 then 0
                when end_at <= now() and status=1 then 1
                end as st
            '));
	}

	public function scopeDaterange($query,$value,$f) {
		$temp=explode('|',$value);
		$s=@$temp[0];
		$e=@$temp[1];
		if($s&&$e) {
			$query=$query->whereRaw(DB::raw("date($f) >= date('$s')"))->whereRaw(DB::raw("date($f) <= date('$e')"));
		}
		return $query;
	}

	public function getDaterangeAttribute() {
		return $this->begin_at.'~'.$this->end_at;
	}

	public function setEmbedAttribute($value) {
		$this->attributes['embed']=$_POST['embed'];
	}

	public function getViewsAttribute() {
		return $this->show;
	}
    /*
     * @subjct 取得最新n個月之曝光率
     * @param int 月數
     */
    public function scopeLastMonth($query,$limitOfMonth=6){
        return $query->join('bepoapp_ad_detail','ad_id','=','id')
            ->whereRaw("month(begin_at) >= month(DATE_SUB(NOW(), INTERVAL $limitOfMonth MONTH))")
            ->groupByRaw("month(begin_at),platform_type")
            ->orderBy('begin_at','desc')
            ->selectRaw("concat(year(begin_at),'-', month(begin_at)) as '月份', sum(if(type!=3,d.show,0)) as '曝光數', sum(if(type!=3,d.click,0)) as '點擊數', (sum(if(type!=3,d.click,0))/sum(if(type!=3,d.show,0))) * 100 as '點擊率', sum(d.show) as '曝光數(含聯播)', sum(d.click) as '點擊數(含聯播)', (sum(d.click)/sum(d.show)) * 100 as '點擊率(含聯播)'");
    }
	public function getdeltaAttribute() {

        $show=0;
            $click=0;
            foreach(AdDetail::where('ad_id','=',$this->id)->orderBy('date','asc')->get() as $data) {
                $show+=$data->show;
                $click+=$data->click;
            }
            $delta='';
            if($click > 0) {
                $delta=$click / $show * 100;
                $delta=round($delta,2);
                if($delta > 0) {
                    $delta.='%';
                } else {$delta='';
                }
            }
            return $delta;

	}

}
