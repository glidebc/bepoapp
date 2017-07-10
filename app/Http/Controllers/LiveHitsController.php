<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Live;
use App\LiveHits;

class LiveHitsController extends Controller {
    public function getIndex() {
        $filter=DataFilter::source(new LiveHits());
        $filter->add('created_at','建立時間','daterange') ;
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('live_id','標題',true)->cell(function($value,$row) {
            return "{$row->live->title}";
        });
        $grid->add('date','日期',true)->style('width:12%');
        $grid->add('views','曝光數',true)->style('width:12%');
        $grid->add('hits','點擊數',true)->style('width:12%');
        $grid->add('created_at','建立時間',true)->style('width:12%');
        $grid->orderBy('id','desc');
        $grid->paginate(config('global.rows_of_page'));
		$grid -> build('crud.datagrid');
        return view('crud.grid',compact('filter','grid'));
    }

}
