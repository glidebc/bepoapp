<?php

namespace App\Model\NewBepoTV;

class ProgramModel extends Model {
	protected $connection='mongodb';
	protected $table='programs';
	protected $embeds=array(
		'categories'=>'\App\Model\NewBepoTV\CategoryModel',
		'zone'=>'\App\Model\NewBepoTV\ZoneModel'
	);

	public function getImageUrlAttribute() {
		return asset('/program/promote/'.$this->promote_url);
	}

	public function setEmbedAttribute($value) {
		$this->attributes['embed']=$_POST['embed'];
	}

}
