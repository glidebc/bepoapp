<?php

namespace App\Model\CTI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use DateHelper;
use Carbon\Carbon;
use Request;
use App\Model\CTI\ProgramCalendar;
use DateTime;

class CalendarCopy extends Model{
    private function getDays($weekNo){
        $now=Carbon::create(date('Y'),1,1);
        $now->addWeeks($weekNo);
        $start=$now->startOfWeek();
        $source=array();
        for($i=0;$i<7;$i++){
            $tmp=$start->copy();
            $source[]=$tmp->addDay($i)->format('Y-m-d');
        }
        return $source;
    }
    public function save(array $options=array()){
        $in=Request::input();
        $source=$this->getDays($in['source_week']);
        $target=$this->getDays($in['target_week']);
        for($i=0;$i<count($source);$i++){
            $date=$source[$i];
            $targetDate=$target[$i];
            ProgramCalendar::where('start_at','like','%'.$targetDate.'%')->where('channel_id','=',$in['channel_id'])->delete();
            $coll=ProgramCalendar::where('start_at','like','%'.$date.'%')->where('channel_id','=',$in['channel_id'])->get();
            foreach($coll as $data){
                $tmp=explode(' ',$data->start_at,2);
                $c=new ProgramCalendar();
                $c->channel_id=$in['channel_id'];
                $c->program_id=$data->program_id;
                $tmp=explode(' ',$data->start_at,2);
                $c->start_at=$targetDate.' '.$tmp[1];
                $tmp=explode(' ',$data->end_at,2);
                $c->end_at=$targetDate.' '.$tmp[1];
                $c->save();
            }
        }
        return true;
    }
}
