<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Bepoapp\Categories;
use Response;
use Cache;

class CategoriesResource extends Controller {
    public function index() {
		$data= Categories::orderBy('priority','asc')->where('enabled','=','1')->get();
		return Response::j(true,$data);
    }
}
