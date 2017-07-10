<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\UserHistories;
use Input;

class HistoryMiddleware {

	public function handle($request,Closure $next) {
		$uh=new UserHistories();
		$uh->user_id=Auth::user()->id;
		$uh->log=$request->ip;
		$uh->category=$request->getPathInfo();
		$uh->log=json_encode([
		'uri'=>$request->getRequestUri(),
		'method'=>$request->getMethod(),
		'pathInfo'=>$request->getPathInfo(),
		'parameters'=>Input::all()],JSON_UNESCAPED_UNICODE);
		$uh->save();
		return $next($request);
	}

}
