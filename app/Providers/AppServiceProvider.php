<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider {
	public function boot() {
		if(config('app.debug')) {
			DB::connection('mongodb')->enableQueryLog();
		}
	}

	public function register() {
	}

}
