<?php

namespace App\Model\NewBepoTV;

class PromoteModel extends Model {
	protected $connection='mongodb';
	protected $table='promote';

	public function getImageUrlAttribute() {
		return asset('/prmote/promote/'.$this->promote_url);
	}

}
