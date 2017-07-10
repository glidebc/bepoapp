<?php

namespace App\Ddbepo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Auth;
use DateHelper;
use Carbon\Carbon;
use App\DD360;

class Videos extends Model {
	protected $connection = 'ddbepo';
    protected $primaryKey = 'auto';
	protected $table = 'video';

    protected $visible=array(
        'id',
        'title',
        'pubdate',
        'image',
        'mobile_url',
        'status',
        'adid'
    );

    protected $appends=array(
        'id',
        'title',
        'pubdate',
        'image',
        'status',
        'adid',
        'type'
    );

	const CREATED_AT = 'creat_datetime';
	const UPDATED_AT = 'edit_datetime';
    public $meta=array();
    public function getIdAttribute(){
    	return $this->source_code_youtube;
        // if(count($this->meta)==0)
            // $this->meta=DD360::getInstance()->get($this->source_code_youtube);
        // return $this->meta['id'];
    }
    public function getTitleAttribute(){
        return $this->v_title;
    }
	
    public function getTypeAttribute(){
    	return $this->v_type=='0'?'vod':'live';
        // if(count($this->meta)==0)
            // $this->meta=DD360::getInstance()->get($this->source_code_youtube);
        // return $this->meta['type'];
    }
    public function getPubdateAttribute(){
    	return date('Y-m-d H:i:s',strtotime($this->creat_datetime));
        // return $this->meta['pubdate'];
    }
	//update 360
	public function sync360() {
		if ($this -> exists) {
			$id=$this->source_code_youtube;
			$v=DD360::getInstance()->get($id);
			if(count($v)>0){
				$this -> mobile_url= $v['mobile_url'];
				$this -> dd_image = $v['image'];
				$this -> save();
			}
		}
	}
	
    
	public function setStatusAttribute($v){
		$this->attributes['status'] = $v;
    }
    public function getStatusAttribute(){
    	// if($this->v_type=='1'){
    		// $this->attributes['status']='streaming';
		// }else{
			// return '';
		// }
		if($this->attributes['v_type']==0)return  '';
		
		$v_active=$this->attributes['v_active'];
		switch($v_active){
			case 1:return 'Streaming';
			case 2:return 'Stop';
			default: return '';
		}

        // if(array_key_exists('status', $this->meta)){
            // return $this->meta['status'];
        // }
        // else {
            // return '';
        // }
    }
    public function getAdidAttribute(){
        return '';
    }
	public function categories() {
		return $this -> belongsToMany('App\Ddbepo\Video_Class','video_class_mapping','auto','vc_id');
	}

    public function scopeLatest(){
        return $this->orderBy('sort','asc')
            ->whereIn('v_active',array(1,2))
            ->orderBy('auto','desc')->take(100);
    }

	public function getThumAttribute(){
	    //'https://deo4bapflag21.cloudfront.net/'
		if(strlen($this->dd_image)){
			return $this->dd_image;
		}
		return '';
	 	// $source_code_youtube=$this->source_code_youtube;
//
//
//
	 	// if($source_code_youtube){
		 	// $info=DD360::getInstance()->get($source_code_youtube);
			// if($info)
			// return $info['image'];
		// }else{
			// return '';
		// }
    }
    //v_small_pic
	public function getImageAttribute(){
	    //'https://deo4bapflag21.cloudfront.net/'
	    $image=$this->v_small_pic;
        if(strlen($image)>0)return asset('video_images/'.$image);

		if(strlen($this->dd_image)){
			return $this->dd_image;
		}
		return asset('images/bg360.png');

        // $source_code_youtube=$this->source_code_youtube;
        // if($source_code_youtube){
            // $info=DD360::getInstance()->get($source_code_youtube);
            // if($info)
                // return $info['image'];
        // }else{
            // return asset('images/bg360.png');
        // }
    }
}
