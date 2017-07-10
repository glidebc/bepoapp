<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.2/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Log;
use App\Posts;

/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }
		/*
		蓋板前六個月各平台成效資料
		SELECT concat(year(begin_at),'-', month(begin_at)) as '月份', sum(if(type!=3,d.show,0)) as '曝光數', sum(if(type!=3,d.click,0)) as '點擊數', (sum(if(type!=3,d.click,0))/sum(if(type!=3,d.show,0))) * 100 as '點擊率', sum(d.show) as '曝光數(含聯播)', sum(d.click) as '點擊數(含聯播)', (sum(d.click)/sum(d.show)) * 100 as '點擊率(含聯播)' FROM `bepoapp_ad` left join bepoapp_ad_detail as d on bepoapp_ad.id=d.ad_id where month(begin_at) >= month(DATE_SUB(NOW(), INTERVAL 6 MONTH)) group by month(begin_at),platform_type order by begin_at desc
		*/

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function index() {
        $data=array(
            'posts_count_today'=>Posts::today()->count(),
            'posts_count_month'=>Posts::month()->count(),
            'posts_count'=>Posts::count(),
            'posts_hits_today'=>Posts::month()->sum('hits'),
            'daily_post_count'=>array()
        );
		foreach(Posts::daily()->get() as $d){
			$ts=strtotime($d->date);
			$date=date('m-d',$ts);
    		$data['daily_post_count'][]=array(
    			$date,$d->total*1
			);
    	}
		sort($data['daily_post_count']);
        return view('home',$data);
    }

}
