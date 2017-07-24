<?php

namespace App\Http\Controllers\Sticker;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Controller extends BaseController {
	use AuthorizesRequests,DispatchesJobs,ValidatesRequests;
	protected $path=null;
	function __construct(Request $request) {
		$this->path=$request->segment(1);
	}
}
