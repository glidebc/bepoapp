<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Input;
use Log;
use App\RoleUser;
use App\Role;
use DB;
use Request;

class User extends Authenticatable {
	use EntrustUserTrait;
	protected $fillable=array(
		'name',
		'email',
		'password'
	);
	protected $hidden=array(
		'password',
		'remember_token'
	);
	public function authors() {
		return $this->hasMany('App\Authors');
	}

	public function roles() {
		return $this->belongsToMany('App\Role','role_user','user_id','role_id');
	}

	public function setPassword1Attribute($value) {
		if(strlen($value)) {
			$this->attributes['password']=bcrypt($value);
		}
	}

	public function getPassword1Attribute() {
		return '';
	}

	//public function setPasswordAttribute($value) {
	//	$this->attributes['password']=bcrypt($value);
	//}

	function scopeProgs() {
		return $this->join('role_user','users.id','=','role_user.user_id')->join('role_prog','role_user.role_id','=','role_prog.role_id')->join('progs','role_prog.prog_id','=','progs.id')->join('menus','menus.id','=','progs.menu_id')->where('progs.enabled','=','1')->orderBy('menus.priority','asc')->orderBy('progs.priority','asc')->select(DB::raw('progs.name as name,progs.path as path,menus.id as menuid,menus.title as menutitle'));
	}

	public function getRoleAttribute($value) {
		$id=$this->attributes['id'];
		$roleuser=RoleUser::where('user_id','=',$id)->firstOrFail();
		$role=Role::find($roleuser->role_id);
		return $role->name;
	}

}
