<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Carbon\Carbon;
use Storage;
use App\shopping_Posts;
use App\Model\Posts;
use App\Model\Categories;
use App\Model\Post_Category;

define('CFG_DBSYNCLOG','db.sync.log');
define('CFG_USER_ID',1);
define('CFG_AUTHOR_ID',1);
define('CFG_SOURCE_ID',1);
ini_set('memory_limit','2048M');

class DBSyncConsole extends Command {
    protected $signature='bepo:dbsync {--init} {--clearposts} {--info}';
    protected $description='bepo:dbsync bepotv資料同步程式';
    public function __construct() {
        parent::__construct();
    }

    public function setOpts() {
        $this->info=$this->option('info') ? true : false;
        $this->initialize=$this->option('init') ? true : false;
        $this->clearposts=$this->option('clearposts') ? true : false;
    }

    public function getAuthor($author_id) {
        $author=DB::connection('platform')->select('select display_name from newbepo.bepo_users where ID=?',array($author_id));
        if(count($author)) {
            return $author[0]->display_name;
        } else {
            return '';
        }
    }

    public function getSourceCates() {
        $categories=DB::connection('platform')->select('select bepo_term_taxonomy.taxonomy,bepo_terms.name,bepo_terms.term_id from newbepo.bepo_term_taxonomy inner join newbepo.bepo_terms
            on newbepo.bepo_term_taxonomy.term_id=newbepo.bepo_terms.term_id
            order by newbepo.bepo_terms.term_id desc ');
        $dict=array();
        $idx=1;
        foreach($categories as $c) {
            if($c->taxonomy == 'category') {
                $dict[$c->term_id]=array(
                    'name'=>$c->name,
                    'taxonomy'=>$c->taxonomy,
                    'idx'=>$idx++,
                    'term_id'=>$c->term_id
                );
            }
        }
        return $dict;
    }

    public function init() {
        Posts::truncate();
        Categories::truncate();
        Post_Category::truncate();
        $this->rebuildTargetCates();
        Storage::delete(CFG_DBSYNCLOG);
    }

    public $cates=array();
    public function getCates($id) {
        $ids=array();
        $sql='select * from bepo_term_relationships where object_id=?';
        foreach(DB::connection('platform')->select($sql,array($id)) as $d) {
            if(array_key_exists($d->term_taxonomy_id,$this->cates)) {
                $ids[]=$d->term_taxonomy_id;
            }
        }
        return $ids;
    }

    public function rebuildTargetCates() {
        Categories::truncate();
        Post_Category::truncate();
        $cates=$this->getSourceCates();
        $i=1;
        foreach($cates as $id=>$data) {
            $name=$data['name'];
            $insert=array(
                'id'=>$id,
                'enabled'=>1,
                'priority'=>0,
                'title'=>$name
            );
            $cate=new Categories();
            $cate->insert($insert);
        }
    }

    public function myinfo($message,$ext='') {
        $temp=array();
        $temp[]=date('Y-m-d H:i:s');
        $temp[]=$message;
        $temp[]=$ext;
        foreach($temp as &$t) {
            if(is_array($t))
                $t=json_encode($t,JSON_UNESCAPED_UNICODE);
        }
        unset($t);
        $this->info(implode(' ',$temp));
    }

    public function update($post_parent) {
        $orig=$this->getPost($post_parent);
        if(!$orig)
            return false;
        $update=array(
            'title'=>$orig->post_title,
            'content'=>$orig->post_content,
            'created_at'=>$orig->post_date,
            'post_at'=>$orig->post_date,
            'author_id'=>CFG_AUTHOR_ID,
            'source_id'=>CFG_SOURCE_ID,
            'type'=>1,
            'priority'=>10,
            'created_user_id'=>CFG_USER_ID,
            'updated_user_id'=>CFG_USER_ID,
            'slug'=>$orig->post_name,
            //'origin_url'     =>$origin_url,
            //'origin_id'     =>$post->ID,
            //'image'            =>$image
        );
        $post=Posts::where('origin_id','=',$post_parent);
        if(count($post) > 0) {
            $post->update($update);
            //$this->myinfo('updated');
        } else {
            //$this->myinfo('update failure');
            return false;
        }
        return true;
    }

    public function getTargetIds() {
        return Posts::groupBy('origin_id')->orderBy('origin_id','asc')->pluck('origin_id')->toArray();
    }

    public function getSourceIds() {
        return shopping_Posts::where('post_status','=','publish')->where('post_type','=','post')->orderBy('ID','asc')->pluck('ID')->toArray();
    }

    public function getTargetList() {
        $list=array();
        foreach(Posts::groupBy('origin_id')->orderBy('origin_id','asc')->get() as $d) {
            $list[]=$d;
        }
        return $list;
    }

    public function getSourceList() {
        $list=array();
        foreach(shopping_Posts::where ( 'post_status','=','publish')
        -> where ( 'post_type' ,'=', 'post')
        -> orderBy('ID','asc')
        -> get() as $d) {
            $list[]=$d;
        }
        return $list;
    }

    public function main() {
        $sids=$this->getSourceIds();
        $this->myinfo('source ids count:'.count($sids));
        $tids=$this->getTargetIds();
        $this->myinfo('target ids count:'.count($tids));
        $diff=array_diff($sids,$tids);
        foreach($diff as $id) {
            $post=$this->getPost($id);
            if($post)
                $this->insertPost($post);
        }

        //Scan to update
        $t=$this->getTargetList();
        $t_array=array();
        foreach($t as $d) {
            $t_array[]=$d->origin_id.'.'.$d->orig_post_modified;
        }
        $s=$this->getSourceList();
        $s_array=array();
        foreach($s as $d) {
            $s_array[]=$d->ID.'.'.$d->post_modified;
        }
        $diff=array_diff($s_array,$t_array);
        $this->myinfo('update count:'.count($diff));
        foreach($diff as $seg) {
            list($id,$ts)=explode('.',$seg,2);
            $post=$this->getPost($id);
            if($post)
                $this->updatePost($post);
        }
        //need to remove
        $diff=array_diff($tids,$sids);
        $this->myinfo('remove count:'.count($diff));
        foreach($diff as $removeId) {
            $target=Posts::where('origin_id','=',$removeId);
            $target->delete();
        }

    }

    public function updatePost($post) {
        $image='';
        if(array_key_exists($post->ID,$this->imageCache)) {
            $image=$this->imageCache[$post->ID];
        } else {
            $image=$this->getImage($post->ID);
            $this->imageCache[$post->ID]=$image;
        }
        $update=array(
            'title'=>$post->post_title,
            'content'=>$post->post_content,
            // 'created_at' => $post['date'],
            'post_at'=>$post->post_date,
            'orig_post_modified'=>$post->post_modified,
            // 'author_id' => AUTHOR_ID,
            // 'source_id' => SOURCE_ID,
            // 'type' => 1,
            // 'priority' => 10,
            // 'created_user_id' => USER_ID,
            // 'updated_user_id' => USER_ID,
            // 'slug' => $post['slug'],
            //'origin_url'     =>$origin_url,
            'image'=>$image
        );
        $target=Posts::where('origin_id','=',$post->ID);
        if(count($target) > 0) {
            //$this->myinfo($post->ID);
            $target->update($update);
            return true;
        } else {
            //$this->myinfo('update failure');
            return false;
        }
    }

    public function handle() {
        $this->setOpts();
        $this->cates=$this->getSourceCates();
        if($this->info) {
            foreach($this->cates as $id=>$data) {
                $info=json_encode($data,JSON_UNESCAPED_UNICODE);
                $this->myinfo($id,$info);
            }
            return;
        } else if($this->initialize) {
            $this->myinfo("--init=$this->initialize");
            $this->init();
            return;
        } else if($this->clearposts) {
            $this->myinfo("--clearposts=$this->clearposts");
            Posts::truncate();
            Categories::truncate();
            Post_Category::truncate();
            Storage::disk('local')->put(CFG_DBSYNCLOG,0);
            return;
        }
        $this->myinfo('dbsynclog='.CFG_DBSYNCLOG);
        $this->main();
        return;

    }

    public $imageCache=array();

    public function getImage($post_id) {
        $sql="select * from bepo_postmeta where post_id=? and meta_key='_thumbnail_id'";
        $meta_list=DB::connection('platform')->select($sql,array($post_id));
        if(count($meta_list) > 0) {
            $data=$meta_list[0];
            $post_id=$data->meta_value;
            $sql="select * from bepo_postmeta where post_id=? and meta_key='_wp_attached_file'";
            $meta_list=DB::connection('platform')->select($sql,array($post_id));
            if(count($meta_list) > 0) {
                $data=$meta_list[0];
                return $data->meta_value;
            };
        }
        return '';
    }

    public function getPost($id) {
        $posts=DB::connection('platform')->select('select * from bepo_posts where ID=? limit 1',array($id));
        return count($posts) > 0 ? $posts[0] : false;
    }

    public function insertPost($post) {
        // @@@@post_type@@@@
        // acf
        // attachment
        // nav_menu_item
        // page
        // post
        // revision
        // vc_grid_item

        // @@@@post_status@@@@
        // auto-draft
        // draft
        // inherit
        // pending
        // private
        // publish
        // trash
        $this->myinfo('C '.$post->post_title);

        $p=Carbon::parse($post->post_date);
        $origin_url=sprintf('http://bepo.ctitv.com.tw/%04d/%02d/%s/',$p->year,$p->month,$post->ID);
        $type=$post->post_type;
        $oid=$post->post_parent;

        $image='';
        if(array_key_exists($post->ID,$this->imageCache)) {
            $image=$this->imageCache[$post->ID];
        } else {
            $image=$this->getImage($post->ID);
            $this->imageCache[$post->ID]=$image;
        }

        $insert=array(
            'title'=>$post->post_title,
            'content'=>$post->post_content,
            'created_at'=>$post->post_date,
            'post_at'=>$post->post_date,
            'author_id'=>CFG_AUTHOR_ID,
            'source_id'=>CFG_SOURCE_ID,
            'type'=>1,
            'orig_post_modified'=>$post->post_modified,
            'priority'=>10,
            'created_user_id'=>CFG_USER_ID,
            'updated_user_id'=>CFG_USER_ID,
            'slug'=>$post->post_name,
            'origin_url'=>$origin_url,
            'origin_id'=>$post->ID,
            'image'=>$image
        );
        $new_post=new Posts;
        $id=$new_post->insertGetId($insert);
        $category=$this->getCates($post->ID);
        foreach($category as $cid) {
            $cate_insert=array(
                'post_id'=>$id,
                'category_id'=>$cid
            );
            Post_Category::insert($cate_insert);
        }
    }

}
