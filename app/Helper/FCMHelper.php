<?php

/*
 TITLE=【影】用生命做實驗！ 他拿火爆NOTE7烤肉 居然有五分熟
 AKEY=AIzaSyBEIcc30xnyzKlqB8oMAlzbxGn540MUjMw
 BODY=http://bepo.ctitv.com.tw/bepoapp/articles/20160914/16104
 IOS_TO=cisCmSQQacQ:APA91bHKO0INaM85L60nRHjTlv4QaQGo-VNAEY8PqiO992CUHYKQ49tG31s4w6FlnvIS9Vnv4R49gcsHxbesPq-rP9H75X3NjBQoiAiab5basRyDk168YWUnaftE89L4PkFtRPNsq3B8
 ANDROID_TO=cIXpXU7Evsc:APA91bGSYKvQKfhlADYueaEkHEy_JrozdwdjI5WBu0URDszNJYUejCVEoV58v35DqsVH4zyvqsMHggT_5VO52P9wmrUXTdcp2ij34ueabe9i2tLcVg7b6TRNzZ3rXuLnhlAujCdyI3Vc

 all:IOS ANDROID

 ANDROID:
 curl -X POST --header "Authorization: key=$(AKEY)" --header "Content-Type: application/json" https://fcm.googleapis.com/fcm/send -d "{\"to\":\"$(ANDROID_TO)\",\"notification\":{\"title\":\"$(TITLE)\",\"body\":\"$(BODY)\",\"icon\":\"push_notification_icon\"}}"

 IOS:
 curl -X POST --header "Authorization: key=$(AKEY)" --header "Content-Type: application/json" https://fcm.googleapis.com/fcm/send -d "{\"to\":\"$(IOS_TO)\",\"notification\":{\"title\":\"$(TITLE)\",\"body\":\"$(BODY)\",\"icon\":\"push_notification_icon\"}}"
 */

namespace App\Helper;
use Log;
use App\Model\FCMDeviceModel;
use App\FcmNotification;
use App\Jobs\Notify;

class FCMBase {
	protected $ch=null;
#	protected $appId='AIzaSyBEIcc30xnyzKlqB8oMAlzbxGn540MUjMw';
	protected $appId='AAAA9jB65ug:APA91bGHOoWP6NSEpiBmzR5kdicBaxA8DE30K17OmN9PhPeBqP5nQiAXsfZktH2kHkE6b4X7O4RLhhN8cV0obE1--ubBtlKlaFlk7n68W1RulBkodOj3Jzn53I3zjG5h_LoGYBUB0q68';
	protected $url=null;
	protected $error=null;
	protected $baseurl='https://fcm.googleapis.com/fcm/send';
	protected $headers=array(
		'Content-Type: application/json',
		'Accept: application/json'
	);
	function __construct() {
		$this->headers[]='Authorization: key='.$this->appId;
		$this->initialize();
		$this->url=$this->baseurl;
	}

	public function initialize() {
		$this->ch=curl_init();
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$this->headers);
	}

	public function get($data=array()) {
		curl_setopt($this->ch,CURLOPT_POST,0);
		curl_setopt($this->ch,CURLOPT_URL,$this->url);
		curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($data,JSON_UNESCAPED_UNICODE));
		$output=curl_exec($this->ch);
		$this->error=curl_error($this->ch);
		$http_code=curl_getinfo($this->ch,CURLINFO_HTTP_CODE);
		return $output;
	}

	public function post($data) {
		curl_setopt($this->ch,CURLOPT_POST,1);
		curl_setopt($this->ch,CURLOPT_URL,$this->url);
		curl_setopt($this->ch,CURLOPT_POSTFIELDS,json_encode($data,JSON_UNESCAPED_UNICODE));
		$output=curl_exec($this->ch);
		echo 'to:'.$data['to'],"\n";
		echo 'output:'.$output."\n";
		$this->error=curl_error($this->ch);
		return $output;
	}

}

class FCMHelper extends FCMBase {

	/*
	 <mesg>
	 Array
	 (
	 [id] => 330
	 [post_id] => 16250
	 [type] => 0
	 [title] => 又有大嬸強逼讓博愛座！ 他被正義魔人氣到在公車上脫褲子
	 [status] => 2
	 [message_id] => 10.254.
	 [content] =>
	 [category_id] => 0
	 [play_at] => 2016-09-17 21:25:03
	 [url] =>
	 [thumbnail_url] =>
	 [begin_at] => 2016-09-17 21:25:02
	 [end_at] => 2016-09-17 21:25:03
	 [ios_enabled] => 1
	 [ios_begin_at] =>
	 [ios_end_at] =>
	 [ios_success] => 0
	 [android_enabled] => 1
	 [android_begin_at] =>
	 [android_end_at] =>
	 [android_success] => 0
	 [created_user_id] => 70
	 [created_at] => 2016-09-17 18:47:15
	 [updated_user_id] => 70
	 [updated_at] => 2016-09-17 21:25:03
	 [post] => Array
	 (
	 [id] => 16250
	 [title] => 又有大嬸強逼讓博愛座！ 他被正義魔人氣到在公車上脫褲子
	 [image] => http://pics.ctitv.com/wpimg/2016/09/f_18350313_1.jpg
	 [pubdate] => 2016/09/17 下午 05:55:37
	 [image_thum] => http://pics.ctitv.com/wpimg/2016/09/f_18350313_1-300x160.jpg
	 [htm_url] => http://bepo.ctitv.com.tw/2016/09/123796/
	 [mobile_url] => http://bepo.ctitv.com.tw/bepoapp/articles/20160917/16250
	 [video] => N
	 [adid] =>
	 )

	 )
	 *
	 *
	 /*
	 ios:title&body=<title>
	 android:title=<title>,body=<mobile_url>
	 */
	static public function pushMessage($mesg) {
		$start=time();
		$fcm=new FCMBase();
		$logFile='fcm.log';
		Log::useDailyFiles(storage_path().'/logs/'.$logFile);

		$return=array(
			'android'=> array(
				'total'=>0,
				'error'=>0,
				'success'=>0
			),
			'ios'=> array(
				'total'=>0,
				'error'=>0,
				'success'=>0
			)
		);

		FCMDeviceModel::orderBy('type','asc')->orderBy('created_at','asc')
		  ->chunk(1000,function($datalist) use ($mesg,$fcm,&$return) {
			foreach($datalist as $data) {
				/* push to queue */
				$f=new FcmNotification();
				$f->notification_id=$mesg['id'];
				$f->fcm_id=$data->id;
				$f->save();
				$job=new Notify($f);

                //new version begin
				if($data->type==0) {
					$return['ios']['success']+=1;
                    dispatch($job);
				}
				else if($data->type==1) {
					$return['android']['success']+=1;
                    dispatch($job);
				}else{

				}
				continue;

                //new version end

				/* end */
				$postData=array(
					'to'=>$data->token,
					'notification'=> array(
						'title'=>'必PO TV',//$mesg['title'],
						'body'=>$mesg['title'],
						'icon'=>'push_notification_icon',
						"click_action"=>"show_push_content"
					)
				);
				//type 1 eq android
				if($data->type==1) {
					$postData['data']=$mesg['post'];
					$postData['notification']['sound']='default';
				}
				//type=0 eq ios
				if($data->type==0) {
					$postData['priority']='high';
					$postData['notification']['sound']='default';
					//$postData['notification']['body']=$mesg['title'];
				}
				//Log::info(json_encode($postData,JSON_UNESCAPED_UNICODE));
				//return format:json
				//ex:
				//{"multicast_id":7031199960829655033,"success":0,"failure":1,"canonical_ids":0,"results":[{"error":"NotRegistered"}]}
				$result=$fcm->post($postData);
				//Log::info('response:'.$result);
				$resultObj=json_decode($result,true);
				if(array_key_exists('success',$resultObj)&&$resultObj['success']) {
					//$data->success+=1;
					//$data=$resultObj['multicast_id'];
					if($data->type==0) {
						$return['ios']['success']+=1;
					}
					else
					if($data->type==1) {
						$return['android']['success']+=1;
					}
				}
				else {
					//$data->error+=1;
					$temp=$resultObj['results'][0];
					//$data->error_msg=$temp['error'];
					if($data->type==0) {
						$return['ios']['error']+=1;
					}
					else
					if($data->type==1) {
						$return['android']['error']+=1;
					}
					$data->delete();
				}
				// $data->save();
				//Log::info(json_encode($postData,JSON_UNESCAPED_UNICODE).' return:'.json_encode($result,JSON_UNESCAPED_UNICODE));
			}
		});
		$end=time();
		$return['ios']['total']=$return['ios']['success']+$return['ios']['error'];
		$return['android']['total']=$return['android']['success']+$return['android']['error'];
		$return['messageId']=date('YmdHis',$start).'-'.$return['ios']['total'].'-'.$return['android']['total'];
		$return['elapsed_time']=$end-$start;
		return $return;
	}

	static public function send($fnModel) {
	    //$logFile='fcm_m.log';
	    //Log::useDailyFiles(storage_path().'/logs/'.$logFile);
		if(!$fnModel){
		      Log::info('null');
            return true;
		}
		$fcm=new FCMBase();
		$nModel=$fnModel->notification;
		$fdModel=$fnModel->device;

		if($nModel&&$fdModel) {
			$postData=array(
				'to'=>$fdModel->token,
				'notification'=> array(
					'title'=>'必PO TV',
					'body'=>$nModel->title,
					'icon'=>'push_notification_icon',
					"click_action"=>"show_push_content"
				)
			);
			//android
			if($fdModel->type==1) {
				//todo
				$postData['data']=$nModel->post;
				$postData['notification']['sound']='default';
			}
			//ios
			if($fdModel->type==0) {
				$postData['priority']='high';
				$postData['notification']['sound']='default';
			}
			Log::info(__CLASS__,$postData);
            //New version
			//return $postData;
			$result=$fcm->post($postData);
            Log::info(__CLASS__.' result='.$result);
			$resultObj=json_decode($result,true);
			if(array_key_exists('success',$resultObj)&&$resultObj['success']) {
			    Log::info('success');
				return true;
			}
            Log::info('fail');
		}else{
		     Log::info('valid data data=',$fnModel->toArray());
		}
		return false;
	}

}
