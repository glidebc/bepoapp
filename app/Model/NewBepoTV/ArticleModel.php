<?php

namespace App\Model\NewBepoTV;

class ArticleModel extends Model {
	protected $connection='mongodb';
	protected $table="articles";
	protected $embeds=array('program'=>'\App\Model\NewBepoTV\ProgramModel');
	//protected $dates = ['created_at'];
}
