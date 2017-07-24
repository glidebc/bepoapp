<?php

/*
 |--------------------------------------------------------------------------
 | Routes File
 |--------------------------------------------------------------------------
 |
 | Here is where you will register all of the routes in an application.
 | It's a breeze. Simply tell Laravel the URIs it should respond to
 | and give it the controller to call when that URI is requested.
 |
 */
Route::get('/','HomeController@index');
/*
 Route::get('/',function() {
 return Redirect::to('login');
 });
 */
/*
 |--------------------------------------------------------------------------
 | Application Routes
 |--------------------------------------------------------------------------
 |
 | This route group applies the "web" middleware group to every route
 | it contains. The "web" middleware group is defined in your HTTP
 | kernel and includes session state, CSRF protection, and more.
 |
 */
//Route::group(array('prefix'=> 'bepoapp'),function() { 
    Route::get('adshow/{id?}','AdController@show')->where(array('id'=>'[0-9]+'));
//});

Route::group(['middleware'=>[
'web',
'auth',
'App\Http\Middleware\ProgsMiddleware',
'history']],function() {
	Route::controller('userhistories','UserHistoriesController');
	Route::controller('users','UsersController');
	Route::controller('manager','ManagerController');
	Route::controller('roles','RolesController');
	Route::controller('post_histories','PostHistoriesController');
	Route::controller('authors','AuthorsController');
	Route::controller('system','SystemController');
	Route::controller('progs','ProgsController');
	Route::controller('tags','TagsController');
	Route::controller('categories','CategoriesController');
	Route::controller('posts','PostsController');
	Route::controller('sources','SourcesController');
	Route::controller('carousel','CarouselController');
	Route::controller('notification','NotificationController');
	Route::controller('live','LiveController');
	Route::controller('ad','AdController');
	Route::controller('ad_report','AdReportController');
	Route::controller('notification_hits','NotificationHitsController');
	Route::controller('live_hits','LiveHitsController');
	Route::controller('announcements','AnnouncementsController');
	Route::controller('menus','MenusController');
	Route::controller('videos','VideosController');
	Route::controller('videoclass','VideoClassController');
	Route::controller('bepocategories','Bepoapp\CategoriesController');
	Route::controller('bepolive','Bepoapp\LiveController');
	Route::controller('bepostar','Bepoapp\StarController');
	Route::controller('bepoevent','Bepoapp\EventController');
	Route::controller('beponewevent','Bepoapp\NewEventController');
	Route::controller('bepotvcategories','Bepotv\CategoriesController');
	Route::controller('bepotvvideos','Bepotv\VideosController');
	Route::controller('bepoversionctl','Bepoapp\VersionCtlController');
	Route::controller('bepohotest','Bepoapp\HotestController');
	Route::controller('tags','TagsController');
	Route::controller('showbar_videos','Showbar\VideosController');
	Route::controller('showbar_videoclass','Showbar\VideoClassController');
	Route::controller('cti_program','CTI\ProgramController');
	Route::controller('cti_program_034','CTI\ProgramCalendarController');
	Route::controller('cti_program_036','CTI\ProgramCalendarController');
	Route::controller('cti_program_041','CTI\ProgramCalendarController');
	Route::controller('cti_program_A1','CTI\ProgramCalendarController');
	Route::controller('cti_program_A2','CTI\ProgramCalendarController');
	Route::controller('cti_program_A3','CTI\ProgramCalendarController');
	/*
	 034 中天娛樂台
	 036 中天綜合台
	 041 中天新聞台
	 A1 北美台
	 A2 亞洲台
	 A3 亞洲資訊台
	 */
	Route::get('logs','\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
	//new bepo website(for video style)
	Route::controller('newbepotv_videos','NewBepoTV\VideosController');
	Route::controller('newbepotv_video_class','NewBepoTV\VideoClassController');
	Route::controller('newbepotv_zone','NewBepoTV\ZoneController');
	Route::controller('newbepotv_program','NewBepoTV\ProgramController');
	Route::controller('newbepotv_tag','NewBepoTV\TagController');
	Route::controller('newbepotv_category','NewBepoTV\CategoryController');
	Route::controller('newbepotv_promote','NewBepoTV\PromoteController');
	Route::controller('app_setting_1','AppSettingController');
	//sticker
	Route::controller('sticker_vendor','Sticker\VendorController');
	Route::controller('sticker_vendor_item','Sticker\VendorItemController');
});

//Route::group(array('prefix'=> 'bepoapp'),function() {
	Route::resource('services/news','BepoAPIController');
	Route::resource('services/news_test','BepoAPIController@news_test');

	Route::any('cti/services/program_calendar','CTI\ProgramCalendarController@info');
	Route::get('services/ad/{id?}','BepoAPIController@ad');
	Route::get('services/ad_update','BepoAPIController@ad_update');
	Route::get('services/notification','BepoAPIController@notification');
	Route::get('services/360','BepoAPIController@channel360');
	Route::get('services/cti_videos','BepoAPIController@channelCtiVideos');
	Route::get('services/event','BepoAPIController@channelEvent');
	Route::get('services/star','BepoAPIController@channelStar');
	Route::get('services/live','BepoAPIController@channelLive');
	Route::get('services/24hours','BepoAPIController@channel24hours');
	Route::get('services/hotest','BepoAPIController@channelHotest');
	Route::get('services/AppCheckVer','BepoAPIController@AppCheckVer');
	Route::get('services/embed/{kind}/{limit?}','BepoAPIController@embed');
	Route::get('search/posts','SearchController@posts');
	Route::get('search/tags','SearchController@tags');
	Route::get('services/fcm/update','FCMController@update');
	Route::get('services/program','CTI\ProgramCalendarController@anyInfo');
	//bepo embeding
	Route::get('appdownload','AppDownload@index');

	Route::resource('services/categories','CategoriesResource');
	Route::resource('services/api','ApiResource');
	Route::get('articles/{date}/{id}/{slug?}','ArticlesController@show')->where(array(
		'date'=>'[0-9]+',
		'id'=>'[0-9]+'
	));

	Route::controller('test','TestController');
	Route::controller('statistic','StatisticController');
//});

Route::group(array('middleware'=>[
	'web',
	'auth',
	'App\Http\Middleware\ProgsMiddleware',
	'history']),function() {
	Route::controller('pet_review','Pet\ReviewController');
	Route::controller('cogi_run_review','Pet\CogiRunController');
});
