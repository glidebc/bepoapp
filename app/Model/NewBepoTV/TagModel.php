<?php

namespace App\Model\NewBepoTV;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class TagModel extends Model {
	protected $connection='mongodb';
	protected $table='tags';
	public function save(array $options=array()) {
		// $isDirty=$this->isDirty();
		// if($isDirty) {
		// $colls=VideoModel::whereIn('tags.name',array($this->name))->get();
		// $this->total=count($colls);
		// }
		return parent::save();
	}

}
