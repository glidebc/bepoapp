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
use Redirect;
use App\Model\CTI\Program;
use App\Model\CTI\ProgramCalendar;
use App\Model\CTI\CalendarForm;
use App\Model\CTI\CalendarCopy;
use View;
use Carbon\Carbon;
use App\Model\AppSetting;

class AppSettingController extends Controller {

	public function anyIndex(Request $request) {
		$pairs=explode('_',$request->segment(1));
		$id=$pairs[2];
		$model=AppSetting::findorFail($id);
		$form=DataForm::source($model);
		$form->submit('更新');
		$form->add('name','名稱','text')->mode('readonly');
		$form->add('description','描述','textarea');
		$form->add('live_id','24小時直播yt id','text')-> rule('required');
		$form->saved(function() use ($form,$request) {
			return redirect(url($this->path))->with('message','更新完成');
		});
		$form->build();
		return $form->view('crud.form',compact('form'));
	}

}
