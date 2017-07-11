<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class ApiResource extends Controller {
	public function index() {
		return ['version'=>1];
	}
}
