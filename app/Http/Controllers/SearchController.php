<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Posts;
use App\Authors;
use App\Categories;
use App\User;
use App\PostHistories;
use App\Sources;
use Auth;
use Input;
use Cviebrock\EloquentTaggable\Models\Tag;

class SearchController extends Controller {
	public function posts() {
		$q=Input::get('q');
		$dl=Posts::search($q)->get();
		return $dl;
	}
	public function tags() {
		$q=Input::get('q');
		$dl=Tag::where('name','like','%'.$q.'%')
			->selectRaw('tag_id,name')->get();
		return $dl;
	}
}
