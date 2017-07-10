<?php namespace App;

use Zizaco\Entrust\EntrustRole;


class Role extends EntrustRole
{
	public function roleuser(){
        return $this->has('App\RoleUser');
    }
    public function progs(){
        return $this->belongsToMany('App\Progs','role_prog','role_id','prog_id');
    }
}