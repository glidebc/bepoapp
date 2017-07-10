<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use DB;
use Carbon;
use Collective\Html\FormFacade as Form;
use App\FcmNotification;
use App\Jobs\Notify;
use App\Helper\FCMHelper;
use Storage;

class TestController extends Controller {
	public function anyNoti() {
		return false;
		$t=0;
		for($i=0;$i<1000;$i++) {
			$m=new FcmNotification();
			$m->notification_id=1;
			$m->fcm_id=1;
			$m->save();
			$job=new Notify($m);
			dispatch($job);
			$t++;
		}
		return $t;

		$ret=FCMHelper::send($m);
		return $ret;
		for($i=0;$i<1000;$i++) {
			// $f=new FcmNotification();
			// $f->notification_id=1;
			// $f->fcm_id=1;
			// $f->save();
			// $job=new Notify($f);
			// dispatch($job);
		}
		return 1;
	}

	public function getTry() {
		$m=new \App\Model\NewBepoTV\ZoneModel();
		$m->name="hello";
		$mm=new \App\Model\NewBepoTV\Article();
		$mm->title="hello article";
		$mm->save();
		dump($mm);
		$m->articles=array($mm);
		$m->save();
		dump($m);
		\App\Model\NewBepoTV\ZoneModel::where('name','=','hello')->delete();
		\App\Model\NewBepoTV\Article::where('title','=','hello article')->delete();
	}

	public function getArticle() {

		for($i=0;$i<10;$i++) {
			$c=new \App\Model\NewBepoTV\CategoryModel();
			$c->name="category3";
			$c->status=1;
			$c1=new \App\Model\NewBepoTV\CategoryModel();
			$c1->name="category4";
			$c1->status=1;
			$a1=new \App\Model\NewBepoTV\ArticleModel();
			$a1->name="article1";
			$a1->status=1;
			$a1->save();
			$a1->categories()->saveMany(array(
				$c,
				$c1
			));
		}

		return $a1;

		//$c->save();
		$a1=new \App\Model\NewBepoTV\ArticleModel();
		$a1->name="article1";
		$a2=new \App\Model\NewBepoTV\ArticleModel();
		$a2->name="article2";
		$c=new \App\Model\NewBepoTV\CategoryModel();
		$c->name="category";
		$c->save();
		$c->articles()->saveMany(array(
			$a1,
			$a2
		));

		return $a1;
		dd($a->categories());

		$s=microtime(true);
		$t=\App\Model\NewBepoTV\ArticleModel::whereIn('categories',array(
			'社會萬象',
			'測試二'
		))->whereStatus(1)->orderBy('created_at','desc')->take(10)->get();
		//$t=\App\Model\NewBepoTV\ArticleModel::take(10)->get();
		//$t=\App\Model\NewBepoTV\ArticleModel::whereIn('categories',array('電視綜藝'))->whereStatus(1)->take(5)->get();
		//$t=\App\Model\NewBepoTV\ArticleModel::whereIn('categories',array('社會萬象'))->whereStatus(1)->take(5)->get();
		return microtime(true)-$s;
	}

	/*
	 {
	 "_id": "58d3920612a52747cb72d753",
	 "name": "娛樂新聞",
	 "status": 1,
	 "color": "#000",
	 "bgcolor": "#FFF",
	 "sort": 100,
	 "updated_at": "2017-03-23 17:14:46",
	 "created_at": "2017-03-23 17:14:46",
	 "article_total": 0
	 },
	 */
	public function getCategories() {
		if(false) {
			\App\Model\NewBepoTV\CategoryModel::truncate();
			foreach(\App\Kind::options() as $key=>$title) {
				$c=new \App\Model\NewBepoTV\CategoryModel();
				$c->name=$title;
				$c->status=1;
				$c->color="#000";
				$c->bgcolor="#FFF";
				$c->sort=100;
				$c->article_total=0;
				$c->save();
			}
		}
		return \App\Model\NewBepoTV\CategoryModel::all();
		return \App\Kind::options();
	}

	public function getShowtv() {
		//Video class
		$c=\App\Video_Class::pluck('vc_name','vc_id')->toArray();
		\App\Model\NewBepoTV\CategoryModel::truncate();
		\App\Model\NewBepoTV\ArticleModel::truncate();
		$opts=array();
		foreach($c as $k=>$v) {
			$cModel=new \App\Model\NewBepoTV\CategoryModel();
			$cModel->name=$v;
			$cModel->status=1;
			$cModel->color='#000';
			$cModel->bgcolor='#FFF';
			$cModel->sort=100;
			$cModel->save();
			$opts[$k]=$cModel->id;
		}
		$t=\App\Videos::orderBy('auto','desc')->take(1000)->get();
		$total=0;
		for($i=0;$i<1;$i++) {
			foreach($t as $m) {
				$vcId=trim($m->vc_id);
				//$categories=@$opts[$vcId];
				$data=$m->toArray();
				unset($data['auto']);
				$data['status']=1;
				$aModel=new \App\Model\NewBepoTV\ArticleModel();
				$aModel->title=$m->v_title;
				$aModel->summary=$m->v_summary;
				$aModel->article=$m->v_article;
				$aModel->status=1;
				$aModel->sort=100;
				$aModel->views=0;
				$aModel->version=1;
				$aModel->yt_id=$m->source_code_youtube;
				//$aModel->categories=$categories;
				$aModel->updated_at=time();
				$aModel->created_at=time();
				$r=$aModel->save();
				$r&&$total++;
			}
		}
		return $total;
	}

	public function anyStorage() {
		Storage::put('hello','Hello');
		echo Storage::get('hello');

		$files=Storage::files();
		foreach($files as $file) {
			if(preg_match('/^hell.+/',$file)) {
				echo $file;
				Storage::delete($file);
			}
		}
		return Storage::files();;
	}

	public function getIndex() {
		return;
		echo '<!doctype html>';
		echo Form::label('標題'),"<br/>";
		echo Form::text('mytext'),"<br/>";
		echo Form::email('myemail'),"<br/>";
		echo Form::date('username'),"<br/>";
		echo Form::time('username'),"<br/>";
		echo Form::select('username'),"<br/>";
		echo Form::select('animal',array(
			'Cats'=> array('leopard'=>'Leopard'),
			'Dogs'=> array('spaniel'=>'Spaniel'),
		)),"<br/>";
		echo Form::selectRange('number',10,20),"<br/>";
		echo Form::selectMonth('month'),"<br/>";
		echo Form::password('username'),"<br/>";
		echo Form::checkbox('name','value'),"<br/>";
		echo Form::radio('name','value'),"<br/>";
		echo Form::file('image'),"<br/>";
		echo link_to_asset('foo/bar.zip',$title=null,$attributes=array(),$secure=null),"<br/>";
		echo link_to_action('TestController@getIndex',$title=null,$parameters=array(),$attributes=array()),"<br/>";
		//echo link_to_route('test', $title = null, $parameters = array(), $attributes = array());
		$test=new \App\Model\Test();
		$test->title='test';
		$test->save();
		dump($test->id);
		dump(\App\Model\Test::count());
		dump(\App\Model\Test::orderBy('title','asc')->take(10)->pluck('title')->toArray());

	}

	public function getAdminPanel() {
		return 'getAdminPanel';
	}

}
