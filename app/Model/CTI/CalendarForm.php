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

/*
 * "program_id" => "1"
 * "start_time" => "2017-05-02"
 * "end_time" => "2017-05-12"
 * "weeks" => array:7 array(
 * 0 => "1"
 * 1 => "2"
 * 2 => "3"
 * 3 => "4"
 * 4 => "5"
 * 5 => "6"
 * 6 => "0"
 * )
 */
class CalendarForm extends Model{
    
    public function batchSave($channelId,$programId,$week,$weeks,$startTime,$endTime){
        $secs=($week-1)*7*24*60*60+(24*3600)+strtotime((date('Y').'-01-01 00:00:00'));
        $prepare=array();
        foreach($weeks as $i){
            $dt=Carbon::create(date('Y',$secs),date('m',$secs),date('d',$secs));
            $date=$dt->addDays($i-1)->format('Y-m-d');
            $prepare[]=array(
                    'channel_id'=>$channelId,
                    'program_id'=>$programId,
                    'start_at'=>$date.' '.$startTime.':00',
                    'end_at'=>$date.' '.$endTime.':00'
            );
        }
        // check
        $errors=array();
        foreach($prepare as $data){
            $s=strtotime($data['start_at']);
            $e=strtotime($data['end_at']);
            for($i=$s;$i<$e-300;$i+=300){
                $date=date('Y-m-d H:i:s',$i+300);
                $pcModel=ProgramCalendar::where('channel_id','=',$data['channel_id'])
                        ->where('start_at','<=',$date)->where('end_at','>=',$date)->first();
                $found=count($pcModel);
                if($found){
                    $pModel=Program::find($pcModel->program_id);
                    $errors[]='該時段資料已有節目('.$pModel->name.')';
                    break;
                }
            }
            if($found){
                break;
            }
        }
        if(count($errors)){
            $this->error=$errors[0];
            return false;
        }
        foreach($prepare as &$data){
            if(preg_match('/([^\s]+?) 24:00:00$/',$data['end_at'],$m)){
                $data['end_at']=$m[1].' '.'23:59:59';
            }
            if(preg_match('/([^\s]+?) 24:00:00$/',$data['start_at'],$m)){
                $data['start_at']=$m[1].' '.'23:59:59';
            }
        }
        unset($data);
        foreach($prepare as $data){
            $c=new ProgramCalendar();
            $c->channel_id=$data['channel_id'];
            $c->program_id=$data['program_id'];
            $c->start_at=$data['start_at'];
            $c->end_at=$data['end_at'];
            $c->save();
        }
        return true;
    }
    public function save(array $options=array()){
        $in=Request::input();
       
       $scopes=array(
            array($in['start_time'],$in['end_time']),
            array(@$in['start_time1'],@$in['end_time1']),
            array(@$in['start_time2'],@$in['end_time2'])
       ); 
       foreach($scopes as $scope){
           if($scope[0]&&$scope[1]){
            $success=$this->batchSave($in['channel_id'],$in['program_id'],$in['week'],$in['weeks'],$scope[0],$scope[1]);
               if(!$success)return false;
           }
       }
       return true;
    }
}
