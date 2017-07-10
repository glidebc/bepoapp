<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

class ApiResource extends Controller {
	public function index() {
		return ['version'=>1];
	}

	public function create() {
	}

	public function store(Request $request) {
	}

	public function show($id) {
	}

	public function edit($id) {
	}

	public function update(Request $request,$id) {
	}

	public function destroy($id) {
	}

}
