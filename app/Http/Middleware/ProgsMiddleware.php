<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Request;
use App\User;
use App\Progs;

class ProgsMiddleware {
	public function handle($request,Closure $next,$guard=null) {
		$uri=Request::segment(1);
		$segs=Request::segments();
		$len=count($segs)-1;
		if($len>0) {
			#$uri=implode('/',$segs);
		}
		$user_id=Auth::user()->id;
		$progModel=User::progs()->where('users.id','=',$user_id)->where('progs.path','=',$uri)->first();
		if(0==count($progModel)) {
			return response('Unauthorized.',401);
		}
        //load title vars to view
		View()->share('site_title',$progModel['name']);
		return $next($request);
	}

}
