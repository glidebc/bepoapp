<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use App\Notification;
use App\Carousel;
use DateHelper;
use Carbon\Carbon;
use Cviebrock\EloquentTaggable\Taggable;

class Posts extends Model {
	use SoftDeletes;
	use Taggable;
    protected $table='posts';
	protected $guarded = array(
		'deleted_at',
		'post_at'
	);
	protected $dates = ['deleted_at'];
	protected $visible = array(
		'id',
		'title',
		'pubdate',
		'image',
		'image_thum',
		'htm_url',
		'mobile_url',
		'video',
		'adid'
	);
	protected $appends = [
	'pubdate',
	'image_thum',
	'htm_url',
	'mobile_url',
	'video',
	'adid'];

	public function author() {
		return $this -> belongsTo('App\Authors');
	}

	public function getImageAttribute() {
		$image = $this -> attributes['image'];
		if ($image)
			return 'http://pics.ctitv.com/wpimg/' . $image;
	}

	public function getImageThumAttribute() {
		$image = $this -> attributes['image'];
		if ($image) {
			$buff = explode('.', $image);
			$url = $buff[0] . '-300x160.' . $buff[1];
			return 'http://pics.ctitv.com/wpimg/' . $url;
		}
	}

	public function getHtmUrlAttribute() {
		return $this -> origin_url;
	}

	public function getMobileUrlAttribute() {
		$p = Carbon::parse($this -> attributes['post_at']);
		$date = sprintf("%04d%02d%02d", $p -> year, $p -> month, $p -> day);
		return url('articles', [
		$date,
		$this -> attributes['id']]);
	}

	public function getAdidAttribute() {
		return '';
	}

	public function getPubdateAttribute() {
		return DateHelper::zhDateTime($this -> attributes['post_at']);
	}

	public function getVideoAttribute() {
		if ($this -> attributes['yt_id'] || $this -> attributes['fb_id'] || $this -> attributes['dm_id'] || $this -> attributes['yk_id'] || $this -> attributes['vimeo_id'] || $this -> attributes['yt_id'])
			return 'Y';
		return 'N';
	}

	public function categories() {
		return $this -> belongsToMany('App\Model\Categories', 'post_category_1', 'post_id', 'category_id');
	}

	//註冊
	public static function boot() {
		parent::boot();
		//判斷是否更新至推撥與跑馬燈資料表
		self::saved(function($post) {
			$is_created = $post -> wasRecentlyCreated;
			//新建立與啟動推撥 則新增至推撥資料表
			//更新與啟動推撥 則新增至推撥資料表
			if (($is_created && $post -> is_notification) || ($post -> is_notification && !$post -> original['is_notification'])) {
				$n = new Notification();
				$n -> post_id = $post -> id;
				$n -> title = $post -> title;
				$n -> content = $post -> content;
				$n -> category_id = $post -> category_id;
				$n -> created_user_id = Auth::user() -> id;
				$n -> updated_user_id = Auth::user() -> id;
				$n -> save();
			}
			//新建立與啟動跑馬燈 則新增至跑馬燈資料表
			//更新與啟動跑馬燈 則新增至跑馬燈資料表
			if (($is_created && $post -> is_carousel) || ($post -> is_carousel && !$post -> original['is_carousel'])) {
				$c = new Carousel();
				$c -> post_id = $post -> id;
				$c -> title = $post -> title;
				$c -> created_user_id = Auth::user() -> id;
				$c -> updated_user_id = Auth::user() -> id;
				$c -> save();
			}
		});

	}

	public function scopeSearch($query, $value) {
		$query -> orderBy('priority', 'asc') -> where('status', '=', '1') -> orderBy('post_at', 'desc') -> take(10);
		if (strlen($value)) {
			$query -> where('title', 'like', '%' . $value . '%');
		}
		return $query;
	}

	//本日
	public function scopeToday() {
		return $this -> whereRaw('post_at >= curdate()');
	}

	//本月
	public function scopeMonth() {
		return $this -> whereRaw('month(post_at) = month(now())')->whereRaw('year(post_at) = year(now())');
	}

	//每日統計資料,限制30筆
	public function scopeDaily() {
		return $this -> groupBy(DB::raw('date(post_at)')) -> select(DB::raw('count(*) as total,date(post_at) as date')) -> orderBy(DB::raw('date(post_at)'), 'desc') -> take(7);
	}

	// public function scopeCategory($catid){
	// if(is_array($catid)){
	// $catid=implode(',',$catid);
	// }
	// return $this->groupBy('id')
	// ->whereRaw("id in (select post_id from post_category where category_id in ($catid))");
	//
	// }
	//
	public function scopeLatest($query) {
		return $query -> orderBy('priority', 'asc') -> where('status', '=', '1') -> orderBy('post_at', 'desc') -> take(100);
	}

	//非新建立資料則更新至版本異動資料表
	//刪除為softing,版本資料不新增
	public function pushHistoryVersion() {
		if ($this -> exists && !$this -> wasRecentlyCreated) {
			$ph = new PostHistories();
			$ph -> content = $this -> original['content'];
			$ph -> post_id = $this -> id;
			$ph -> user_id = $this -> updated_user_id;
			$ph -> author_id = $this -> author_id;
			$ph -> version = $this -> version;
			$ph -> save();
			$this -> version += 1;
			$this -> save();
		}
	}

}
