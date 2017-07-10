<?php

namespace App\Http\Controllers\CTI;

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
use DateTimeZone;

class ProgramCalendarController extends Controller {
	private $weeks=array(
		1=>'周一',
		2=>'周二',
		3=>'周三',
		4=>'周四',
		5=>'周五',
		6=>'周六  ',
		7=>'周日'
	);
	private $weekOpts=array();
	function __construct() {
		$this->path=request()->segment(1);
		$pairs=explode('_',$this->path);
		$channelId=$pairs[2];
		$timeZone=$channelId=='A1'?'America/Los_Angeles':'';
		$this->weekOpts=$this->getWeekOpts($timeZone);
		$this->weekNO=$this->getWeekNO($timeZone);
	}

	public function anyAdd(Request $request) {
		$pairs=explode('_',$request->segment(1));
		$channelId=$pairs[2];
		$timeOpts=array(''=>'選擇時間');
		for($i=0;$i<=(24*60);$i+=15) {
			$t=substr('0'.floor($i/60),-2).':'.substr('0'.$i%60,-2);
			$timeOpts[$t]=$t;
		}

		$filter='';
		$pOpts=array();
		foreach(Program::get() as $p) {
			$pOpts[$p->id]=$p->name;
		}
		$form=DataForm::source(new CalendarForm());
		$form->submit('新增');
		$form->link($this->path,"回列表","TR")->back();
		$form->add('program_id','節目','select')->options($pOpts)->attr('size',7)->rule('required');

		$now=Carbon::now();
		$this->weekNO=$now->weekOfYear;
		$this->weekOpts=array();
		for($i=0;$i<52;$i++) {
			$this->weekOpts[$i+1]='第'.($i+1).'周';
			if($i+1==$this->weekNO) {
				$this->weekOpts[$i+1].=' (目前)';
			}
		}
		$form->add('week','周','select')->options($this->weekOpts)->attr('size',1)->rule('required')->insertValue($this->weekNO);
		$form->date('start_time','時間1(起)','select')->options($timeOpts)->attr('size',1)->rule('required');
		$form->date('end_time','時間1(迄)','select')->options($timeOpts)->attr('size',1)->rule('required|after:start_time');

		$form->date('start_time1','時間2(起)','select')->options($timeOpts)->attr('size',1);
		$form->date('end_time1','時間2(迄)','select')->options($timeOpts)->attr('size',1)->rule('required_with:start_time1|after:start_time1');

		$form->date('start_time2','時間3(起)','select')->options($timeOpts)->attr('size',1);
		$form->date('end_time2','時間3(迄)','select')->options($timeOpts)->attr('size',1)->rule('required_with:start_time2|after:start_time2');

		$form->add('weeks','星期','multiselect')->options($this->weeks)->attr('size',7)->rule('required');
		$form->add('channel_id','','hidden')->insertValue($channelId);
		$timeOpts=array();
		$form->saved(function() use ($form,$request) {
			// redirect()->back()->withInput()->withErrors(array('remote:'.$resp->response_data));
			return redirect(url($this->path.'/add'))->with('message','新增完成');
		});
		$form->build();
		return $form->view('crud.form',compact('form'));
	}

	private function getWeekNO($timeZone) {
		$this->weekNO=0;
		if($timeZone) {
			$this->weekNO=Carbon::now(new DateTimeZone('America/Los_Angeles'))->weekOfYear;
		}
		else {
			$this->weekNO=Carbon::now()->weekOfYear;
		}
		return $this->weekNO;

	}

	private function getWeekOpts($timeZone) {
		$this->weekNO=$this->getWeekNO($timeZone);
		$this->weekOpts=array();
		for($i=0;$i<52;$i++) {
			$this->weekOpts[$i+1]='第'.($i+1).'周';
			if($i+1==$this->weekNO) {
				$this->weekOpts[$i+1].=' (目前)';
			}
		}
		return $this->weekOpts;
	}

	public function anyCopy(Request $request) {
		$pairs=explode('_',$request->segment(1));
		$channelId=$pairs[2];
		$form=DataForm::source(new CalendarCopy());
		$form->submit('複製');
		$form->link($this->path,"回列表","TR")->back();

		$form->add('source_week','周','select')->options($this->weekOpts)->attr('size',1)->rule('required')->insertValue($this->weekNO);
		$form->add('target_week','周','select')->options($this->weekOpts)->attr('size',1)->rule('required')->insertValue($this->weekNO+1);
		$form->add('channel_id','','hidden')->insertValue($channelId);
		$timeOpts=array();
		$form->saved(function() use ($form,$request) {
			return redirect(url($this->path))->withErrors(array('完成'));
		});
		$form->build();
		return $form->view('crud.form',compact('form'));
	}

	public function anyDelete(Request $request,$id) {
		$model=ProgramCalendar::find($id);
		if($model)
			$model->delete();
		return 1;
	}

	public function anyInfo(Request $request) {
		$channelId='';
		if($request->has('channel_id')) {
			$channelId=$request->input('channel_id');
		}
		else {
			$pairs=explode('_',$request->segment(1));
			$channelId=$pairs[2];
		}
		$model=ProgramCalendar::join('cti_program','program_id','=','cti_program.id')->where('cti_program_calendar.channel_id','=',$channelId)->selectRaw('cti_program_calendar.*,cti_program.color,cti_program.name,cti_program.url,cti_program.level');
		$start=$request->has('start')?$request->input('start'):date('Y-m-d');
		$end=$request->has('end')?$request->input('end'):date('Y-m-d');
		$model=$model->whereRaw("date(start_at) >= date('$start')");
		$model=$model->whereRaw("date(start_at) <= date('$end')");
		// if($request->has('channel_id')) {
		// $model=$model->where('channel_id','=',$request->input('channel_id'));
		// }
		$datalist=array();
		$levels=config('cti.level');
		$channels=config('cti.channel');
		foreach($model->get() as $d) {
			$s=str_replace('23:59:59','24:00:00',$d->start_at);
			$s=preg_replace('/[^\s]+\s(\d{1,2}):(\d{1,2}):\d{1,2}/','$1:$2',$s);
			$e=str_replace('23:59:59','24:00:00',$d->end_at);
			$e=preg_replace('/[^\s]+\s(\d{1,2}):(\d{1,2}):\d{1,2}/','$1:$2',$e);
			$datalist[]=array(
				'id'=>$d->id,
				'title'=>$d->name,//.' '.$s.' - '.$e,
				'start'=>$d->start_at,
				'end'=>$d->end_at,
				// 'url'=>$d->url,
				'color'=>$d->color,
				'className'=>'program-level-'.$d->level.' program-channel-'.$d->channel_id,
				'editable'=>false,
				'levelName'=>array_get($levels,$d->level,null),
				'level_id'=>$d->level,
				'channelName'=>array_get($channels,$d->channel_id,null),
				'channel_id'=>$d->channel_id
			);
		}
		$resp=response()->json($datalist,200,['Content-type'=>'application/json; charset=utf-8'],JSON_UNESCAPED_UNICODE);
		if($request->has('callback'))
			$resp->setCallback($request->input('callback'));
		return $resp;
	}

	public function getIndex(Request $request) {
		$filter='';
		$grid=View::make('program.index',array());
		return view('crud.grid',compact('filter','grid'));
	}

	public function anyEdit(Request $request) {
		$edit=DataEdit::source(new ProgramCalendar());
		$edit->link($request->segment(1),"回列表","TR")->back();
		$channels= array(''=>'頻道')+config('cti.channel');
		$edit->add('channel_id','頻道','select')->options($channels);
		$edit->add('start_date','撥出日期','date')->rule('required');
		$hours=array();
		for($i=0;$i<(24*60);$i+=30) {
			$min=$i%60;
			if($i>=60)
				$hour=($i-$min)/60;
			else
				$hour=0;
			$h=substr('00'.$hour,-2);
			$m=substr('00'.$min,-2);
			$hours[$h.$m.'00']=$h.':'.$m;
		}
		$edit->add('start_time','撥出時間(起)','select')->options($hours)->rule('required');
		$edit->add('end_time','撥出時間(迄)','select')->options($hours)->rule('required|after:start_time');
		$opts= array(''=>'節目')+Program::all()->pluck('name','id')->toArray();
		$edit->add('progam_id','節目','select')->options($opts);
		$edit->build('crud.dataform');
		return $edit->view('crud.edit',compact('edit'));
	}

}
