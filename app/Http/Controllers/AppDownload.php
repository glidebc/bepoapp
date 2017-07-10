<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Response;
use Input;
use Carbon\Carbon;
use DB;
use App\Posts;
use App\Bepoapp\Categories;
use App\Bepoapp\Live;
use App\Bepoapp\Event;
use App\Bepoapp\Star;
use App\Bepoapp\AppName;
use App\Ddbepo\Videos;

use DateHelper;
use Cache;
use App\DD360;

class AppDownload extends Controller
{
   
	public function index()
    {
        return Response::view('appdownload.index');
    }

}
