<?php

namespace App\Model\NewBepoTV;
use App\Model\NewBepoTV\ProgramModel;

class VideoClassModel extends Model {
	protected $connection='mongodb';
	protected $table="video_classes";
	protected $embedsOf=array('video_class'=>'\App\Model\NewBepoTV\ProgramModel');

	public function updateEmbedsOf() {
		foreach($this->embedsOf as $fname=>$class) {
			$data=$this->toArray();
			$m=call_user_func_array($class.'::whereIn',array(
				$fname.'._id',
				array($data['_id'])
			));
			//to update

			//new \MongoDB\BSON\ObjectID($v);
			// $vcId=$data['_id'];
			// $parentId=$data[$fname];
			// dump($vcId);
			// dump($parentId);

		}
		// $pColl=ProgramModel::where('_id','=',$pId)->get();
		// foreach($pColl as $pModel) {
		// $idx=-1;
		// if(is_array($pModel->video_class)) {
		// foreach($pModel->video_class as $i=>$vc) {
		// if($vc['_id']==$pId) {
		// $idx=$i;
		// break;
		// }
		// }
		// }
		// else {
		// $pModel->video_class=array();
		// }
		// $data=array(
		// '_id'=>$pId,
		// 'name'=>$this->name
		// );
		// if($idx>0) {
		// $pModel->video_class[$idx]=$data;
		// }
		// else {
		// $pModel->video_class=$data;
		// }
		// $pModel->save();
		// }
	}

	public function save(array $options=[]) {
		$saved=parent::save();
		if($saved) {
			$this->updateEmbedsOf();
		}
	}

}
