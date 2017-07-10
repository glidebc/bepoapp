<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
	public $table='menus';
	public function created_user(){
        return $this->has('App\User');
    }
	public function updated_user(){
        return $this->has('App\User');
    }
}