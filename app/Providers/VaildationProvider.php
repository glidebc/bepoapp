<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class VaildationProvider extends ServiceProvider {
	public function boot() {
		Validator::extend('required_if_alert_title',function($attribute,$value,$parameters,$validator) {
			dump($attribute);
			dump($value);
			dump($parameters);
			dd($validator);
			dd(2);
			return false;
		});

		Validator::replacer('required_if',function($message,$attribute,$rule,$parameters) {
			$text=str_replace(':value',$parameters[3],$message);
			$text=str_replace(':other',$parameters[2],$text);
			return $text;
		});
	}

	public function register() {

	}

}
