<?php

namespace App\Model\NewBepoTV;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class CategoryModel extends Model {
	protected $connection='mongodb';
	protected $table="categories";
	protected $primaryKey='_id';
	protected $appends=array('article_total');
	public function articles() {
		return $this->hasMany('App\Model\NewBepoTV\ArticleModel','categories');
	}

	public function getArticleTotalAttribute() {
		$m=\App\Model\NewBepoTV\ArticleModel::whereIn('categories._id',array($this->id))->count();
		return $m?$m:0;
	}

}
