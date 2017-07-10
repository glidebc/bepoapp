<?php

namespace App\Model\NewBepoTV;

use App\Model\NewBepoTV\TagModel;

class VideoModel extends Model {
    protected $connection='mongodb';
    protected $table="videos";
    protected $embeds=array(
        'program'=>'\App\Model\NewBepoTV\ProgramModel',
        'video_class'=>'\App\Model\NewBepoTV\VideoClassModel'
    );
    protected $attributes=array(
        'hits'=>0,
        'views'=>0,
        'length'=>0,
        'episode'=>0,
        'hits_plus'=>0
    );
    protected $appends=array('tags');
    //protected $dates = ['created_at'];
    public function save(array $options=array()) {
        $isDirty=$this->isDirty('tags');
        if($isDirty) {
            $tags=array_get($this->getOriginal(),'tags',array());
            if(!is_array($tags))
                $tags=array();
            $oldTags=array();
            foreach($tags as $t) {$oldTags[]=$t['name'];
            }
            $newTags=explode(',',$this->tags);
            $added=array_diff($oldTags,$newTags);
            $deleted=array_diff($newTags,$oldTags);
            $updates=$added + $deleted;
            foreach($updates as $tagname) {
                $colls=VideoModel::whereIn('tags.name',array($tagname))->get();
                $m=TagModel::where('name','=',$tagname)->first();
                $m->total=count($colls);
                $m->save();
            }
        }
        return parent::save();
    }

    public function setTagsAttribute($value) {
        $tags=array();
        foreach(explode(',',$value) as $tagName) {
            $m=TagModel::where('name','=',$tagName)->first();
            if(!count($m)) {
                $m=new TagModel();
                $m->name=$tagName;
                $m->status='1';
                $m->save();
            }
            $tag=array(
                '_id'=>new \MongoDB\BSON\ObjectID($m->_id),
                'name'=>$tagName
            );
            $tags[]=$tag;
        }
        $this->attributes['tags']=$tags;
    }

    public function getTagsAttribute() {
        $tags=array();
        if(is_array(@$this->attributes['tags'])) {
            foreach($this->attributes['tags'] as $tag) {
                $tags[]=$tag['name'];
            }
        }
        return implode(',',$tags);
    }

}
