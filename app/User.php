<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Input;
use Log;
use App\RoleUser;
use App\Role;
use DB;

class User extends Authenticatable {
	use EntrustUserTrait;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
	'name',
	'email',
	'password', ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	'password',
	'remember_token', ];
	public function authors() {
		return $this -> hasMany('App\Authors');
	}

	public function roles() {
		return $this -> belongsToMany('App\Role','role_user','user_id','role_id');
	}
	public function setPasswordAttribute($value) {
		$this -> attributes['password'] = bcrypt($value);
	}

	function scopeProgs(){
		$s= $this->join('role_user','users.id','=','role_user.user_id')
			->join('role_prog','role_user.role_id','=','role_prog.role_id')
			->join('progs','role_prog.prog_id','=','progs.id')
			->join('menus','menus.id','=','progs.menu_id')
			->where('progs.enabled','=','1')
			->orderBy('menus.priority','asc')
            ->orderBy('progs.priority','asc')
			->select(DB::raw('progs.name as name,progs.path as path,menus.id as menuid,menus.title as menutitle'));
		return $s;
	}

	public function getRoleAttribute($value) {
		$id = $this -> attributes['id'];
		$roleuser = RoleUser::where('user_id', '=', $id) -> firstOrFail();
		$role = Role::find($roleuser -> role_id);
		return $role -> name;
	}

}
