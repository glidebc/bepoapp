<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use Response;

class ResponseProvider extends ServiceProvider {
	public function boot() {
		Response::macro('j', function ($result,$data) {
			$r=$result?'true':'false';
			return Response::json(
            ['result'=>$result,'data'=>$data ],200,['Content-type'=> 'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
		});
	}
	public function register() {
	}
}
