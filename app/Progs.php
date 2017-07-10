<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Progs extends Model {
    function scopeOptions($query) {
        return $query->orderBy('menus.priority','asc')
            ->orderBy('progs.priority','asc')
            ->leftJoin('menus','progs.menu_id','=','menus.id')
            ->select(DB::raw('progs.name,progs.id,menus.title as menuname,menus.title || progs.name as fullname'))->get();
    }
}
