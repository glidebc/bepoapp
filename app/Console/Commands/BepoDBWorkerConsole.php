<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Posts;
use App\Categories;
use Carbon\Carbon;
use Storage;
use App\Shopping_Posts;


define('DBSYNCLOG', 'dbsync.log');
define('USER_ID', 1);
define('AUTHOR_ID', 1);
define('SOURCE_ID', 1);
ini_set('memory_limit', '2048M');

class BepoDBWorkerConsole extends Command {
	protected $signature = 'bepo.syncdb {--init} {--clearposts}';
	protected $description = 'bepo syncdb';
	public function __construct() {
		parent::__construct();
	}
	public function getAuthor($author_id) {
		$author = DB::connection('platform') -> select('select display_name from platform.shopping_users where ID=?', [$author_id]);
		if (count($author)) {
			return $author[0] -> display_name;
		}
		else {
			return '';
		}
	}

	public function getCategoryDict() {
		$categories = DB::connection('platform') -> select('select shopping_term_taxonomy.taxonomy,shopping_terms.name,shopping_terms.term_id from platform.shopping_term_taxonomy inner join platform.shopping_terms
            on platform.shopping_term_taxonomy.term_id=platform.shopping_terms.term_id
            order by platform.shopping_terms.term_id desc ');
		$categories_dict = array();
		$idx = 1;
		foreach ($categories as $c) {
			if ($c -> taxonomy == 'category') {
				$categories_dict[$c -> term_id] = array(
					'name' => $c -> name,
					'taxonomy' => $c -> taxonomy,
					'idx' => $idx++
				);
			}
		}
		return $categories_dict;
	}

	public function init() {
		DB::table('posts') -> truncate();
		DB::table('post_category') -> truncate();
		$this -> regenCaetgories();
		Storage::delete(DBSYNCLOG);
	}

    public function getPost1($id){
        $cmd="http://bepo.ctitv.com.tw/wp-json/posts/$id?app_id=beposync&app_token=cj;6rup4g66007766";
        //$cmd="/usr/local/bin/wp --allow-root --path=/home/ctitv/ctitvweb --format=json post get $id";
        //$post=json_decode(`$cmd`);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$cmd);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_HEADER, false);
        $output=curl_exec($ch);
        $post=json_decode($output,true);
        curl_close($ch);
        if(!$post)return false;
        return array_key_exists('ID',$post)?$post:false;
    }
	public $categories_dict=array();
	public function getCategories($id) {
		$sql = 'select * from shopping_term_relationships where object_id=?';
		$term_ids = array();
		foreach (DB::connection('platform')->select($sql,[$id]) as $d) {
			if (array_key_exists($d -> term_taxonomy_id, $this->categories_dict)) {
				$temp = $this->categories_dict[$d -> term_taxonomy_id];
				if ($temp) {
					$term_ids[] = $temp['idx'];
				}
			}
		}
		return $term_ids;
	}

	public function regenCaetgories() {
		DB::table('categories') -> truncate();
		$c_dict = $this -> getCategoryDict();
		$i = 1;
		foreach ($c_dict as $term_id => $c) {
			$name = $c['name'];
			$insert = array(
				'id' => $i++,
				'enabled' => 1,
				'priority' => 0,
				'title' => $name
			);
			$cate = new Categories();
			$cate -> insert($insert);
		}
	}

	public function myinfo($message) {
		$this -> info(date('Y-m-d H:i:s') . ' ' . $message);
	}

	public function update($post_parent) {
		$orig = $this -> getPost($post_parent);
		if (!$orig)
			return false;
		$update = array(
			'title' => $orig -> post_title,
			'content' => $orig -> post_content,
			'created_at' => $orig -> post_date,
			'post_at' => $orig -> post_date,
			'author_id' => AUTHOR_ID,
			'source_id' => SOURCE_ID,
			'type' => 1,
			'priority' => 10,
			'created_user_id' => USER_ID,
			'updated_user_id' => USER_ID,
			'slug' => $orig -> post_name,
			//'origin_url'     =>$origin_url,
			//'origin_id'     =>$post->ID,
			//'image'            =>$image
		);
		$post = Posts::where('origin_id', '=', $post_parent);
		if (count($post) > 0) {
			$post -> update($update);
			//$this->myinfo('updated');
		}
		else {
			//$this->myinfo('update failure');
			return false;
		}
		return true;
	}
    public function getTargetIds(){
        return Posts::groupBy('origin_id')->orderBy('origin_id','asc')->pluck('origin_id')->toArray();
    }
	
	
    public function getSourceIds() {
        return Shopping_Posts::where ( 'post_status','=','publish')
            -> where ( 'post_type' ,'=', 'post')
            -> orderBy('ID','asc')
            -> pluck('ID')
            -> toArray();
    }
	
    public function getTargetList(){
        $list=array();
        foreach ( Posts::groupBy('origin_id')->orderBy('origin_id','asc')->get() as $d){
            $list[]=$d;
        }
        return $list;
    }
    public function getSourceList() {
        $list=array();
        foreach (Shopping_Posts::where ( 'post_status','=','publish')
            -> where ( 'post_type' ,'=', 'post')
            -> orderBy('ID','asc')
            -> get() as $d){
            $list[]=$d;
        }
        return $list;
    }
    public function main(){
        $sids=$this->getSourceIds();
        $this->myinfo('source ids count:'.count($sids));
        $tids=$this->getTargetIds();
        $this->myinfo('target ids count:'.count($tids));
        $diff=array_diff($sids,$tids);
        foreach($diff as $id){
            $post=$this->getPost($id);
            if($post)
                $this->insert($post);
        }

        //Scan to update
        $t=$this->getTargetList();
        $t_array=array();
        foreach($t as $d){
            $t_array[]=$d->origin_id.'.'.$d->orig_post_modified;
        }
        $s=$this->getSourceList();
        $s_array=array();
        foreach($s as $d){
            $s_array[]=$d->ID.'.'.$d->post_modified;
        }
        $diff=array_diff($s_array,$t_array);
        $this->myinfo('update count:'.count($diff));
		foreach( $diff as $seg){
			list($id,$ts)=explode('.',$seg,2);
			$post=$this->getPost($id);
			if($post)
                $this->update1($post);
		}
		//need to remove
		$diff=array_diff($tids,$sids);
		$this->myinfo('remove count:'.count($diff));
		foreach( $diff as $removeId){
			$target = Posts::where('origin_id', '=', $removeId);
			$target->delete();
		}
		
    }

    public function update1($post) {
		$image='';
			if( array_key_exists($post -> ID, $this->imageCache) ){
				$image=$this->imageCache[$post -> ID];
			}else{
				$image = $this -> getImage($post -> ID);
				$this->imageCache[$post -> ID]=$image;
			}
        $update = array(
            'title' => $post->post_title,
            'content' => $post->post_content,
            // 'created_at' => $post['date'],
            'post_at' => $post->post_date,
            'orig_post_modified'=> $post->post_modified,
            // 'author_id' => AUTHOR_ID,
            // 'source_id' => SOURCE_ID,
            // 'type' => 1,
            // 'priority' => 10,
            // 'created_user_id' => USER_ID,
            // 'updated_user_id' => USER_ID,
            // 'slug' => $post['slug'],
            //'origin_url'     =>$origin_url,
            'image'            =>$image
        );
        $target = Posts::where('origin_id', '=', $post->ID);
        if (count($target) > 0) {
        	//$this->myinfo($post->ID);
            $target -> update($update);
            return true;
        }
        else {
            //$this->myinfo('update failure');
            return false;
        }
    }
	public function handle() {
		$this->categories_dict = $this -> getCategoryDict();
		$this -> myinfo('dbsynclog=' . DBSYNCLOG);
		$initialize = $this -> option('init') ? true : false;
		$this -> myinfo("--init=$initialize");
		if ($initialize) {
			$this -> init();
			return;
		}

		$clearposts = $this -> option('clearposts') ? true : false;
        $this -> myinfo("--clearposts=$clearposts");

		if ($clearposts) {
			DB::table('posts') -> truncate();
			DB::table('post_category') -> truncate();
			Storage::disk('local') -> put(DBSYNCLOG, 0);
		}

        $this->main();
        return;

		if (!Storage::disk('local') -> has(DBSYNCLOG)) {
			Storage::disk('local') -> put(DBSYNCLOG, 0);
		}
		$last_id = Storage::disk('local') -> get(DBSYNCLOG);
		$this -> myinfo("last id=$last_id");
		$posts = $this -> getNewPostList($last_id);
		foreach ($posts as $post) {
            $p=$this->getPost1($post->ID);
            if($p && $p['type']=='post'){
                if($p['status']=='publish'){
                    $data = Posts::where('origin_id', '=', $post->ID)->get();
                    if (count($data) > 0) {
                        $this -> myinfo('U '.$p['title']);
                        $this->update1($p);
                    }
                    else {
                        $this -> myinfo('C '.$p['title']);
                        $this->insert1($p);
                    }
                }else{
                    $this -> myinfo('D '.$p['title']);
                    $target = Posts::where('origin_id', '=', $post->ID);
                    $target -> update(array('status'=>0));
                }
            }
            Storage::disk('local') -> put(DBSYNCLOG, $post -> ID);
            continue;

			if ($post -> post_parent > 0) {
				//update
				//$this->myinfo('updating');
				$success = $this -> update($post -> post_parent);
				if (!$success) {
					//insert parent
					//$this->myinfo('updating to insert');
					//load data
					$opost = $this -> getPost($post -> post_parent);
					if ($opost) {
						$this -> insert($opost);
					}
					else {
						//$this->myinfo('............');
					}
				}
			}
			else {
				//insert
				$this -> insert($post);
			}
			Storage::disk('local') -> put(DBSYNCLOG, $post -> ID);
		}
	}

	public $imageCache=array();

	public function getImage($post_id) {
		$sql = "select * from shopping_postmeta where post_id=? and meta_key='_thumbnail_id'";
		$meta_list = DB::connection('platform') -> select($sql, [$post_id]);
		if (count($meta_list) > 0) {
			$data = $meta_list[0];
			$post_id = $data -> meta_value;
			$sql = "select * from shopping_postmeta where post_id=? and meta_key='_wp_attached_file'";
			$meta_list = DB::connection('platform') -> select($sql, [$post_id]);
			if (count($meta_list) > 0) {
				$data = $meta_list[0];
				return $data -> meta_value;
			};
		}
		return '';
	}

	public function getPost($id) {
		$posts = DB::connection('platform') -> select('select * from shopping_posts where ID=? limit 1', [$id]);
		return count($posts) > 0 ? $posts[0] : false;
	}

	public function getNewPostList($last_id) {
		$posts = DB::connection('platform') -> select('select * from shopping_posts where ID > ? and post_type not in("nav_menu_item","page","attachment")  order by ID asc', [$last_id]);
		return $posts;
	}

    public function insert1($post){
            $category = $this -> getCategories($post['ID']);

            $p = Carbon::parse($post['date']);
            $origin_url = sprintf('http://bepo.ctitv.com.tw/%04d/%02d/%s/', $p -> year, $p -> month, $post['ID']);
            $type = $post['type'];
            $oid = $post['ID'];

            $image='';
            if( array_key_exists($post['ID'], $this->imageCache) ){
                $image=$this->imageCache[$$post['ID']];
            }else{
                $image = $this -> getImage($post['ID']);
                $this->imageCache[$post['ID']]=$image;
            }

            $insert = array(
                'title' => $post['title'],
                'content' => $post['content'],
                'created_at' => $post['date'],
                'post_at' => $post['date'],
                'author_id' => AUTHOR_ID,
                'source_id' => SOURCE_ID,
                'type' => 1,
                'priority' => 10,
                'created_user_id' => USER_ID,
                'updated_user_id' => USER_ID,
                'slug' => $post['slug'],
                'origin_url' => $origin_url,
                'origin_id' => $post['ID'],
                'image' => $image
            );
            $new_post = new Posts;
            $id = $new_post -> insertGetId($insert);
            foreach ($category as $c_id) {
                $cate_insert = array(
                    'post_id' => $id,
                    'category_id' => $c_id
                );
                DB::table('post_category') -> insert($cate_insert);
            }
    }
	public function insert($post) {
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
            $this -> myinfo('C '.$post -> post_title);
			$category = $this -> getCategories($post -> ID);

			$p = Carbon::parse($post -> post_date);
			$origin_url = sprintf('http://bepo.ctitv.com.tw/%04d/%02d/%s/', $p -> year, $p -> month, $post -> ID);
			$type = $post -> post_type;
			$oid = $post -> post_parent;

			$image='';
			if( array_key_exists($post -> ID, $this->imageCache) ){
				$image=$this->imageCache[$post -> ID];
			}else{
				$image = $this -> getImage($post -> ID);
				$this->imageCache[$post -> ID]=$image;
			}

			$insert = array(
				'title' => $post -> post_title,
				'content' => $post -> post_content,
				'created_at' => $post -> post_date,
				'post_at' => $post -> post_date,
				'author_id' => AUTHOR_ID,
				'source_id' => SOURCE_ID,
				'type' => 1,
				'orig_post_modified'=>$post->post_modified,
				'priority' => 10,
				'created_user_id' => USER_ID,
				'updated_user_id' => USER_ID,
				'slug' => $post -> post_name,
				'origin_url' => $origin_url,
				'origin_id' => $post -> ID,
				'image' => $image
			);
			$new_post = new Posts;
			$id = $new_post -> insertGetId($insert);
			foreach ($category as $c_id) {
				$cate_insert = array(
					'post_id' => $id,
					'category_id' => $c_id
				);
				DB::table('post_category') -> insert($cate_insert);
			}
	}

}
