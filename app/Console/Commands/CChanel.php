<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use BluemixHelper;
use Log;
use App\Model\NewBepoTV\VideoModel;
use App\Model\NewBepoTV\ProgramModel;
use App\Model\NewBepoTV\VideoClassModel;

class CChanel extends Command {
	protected $signature='cchanel';
	protected $description='cchanel get';
	public function __construct() {
		parent::__construct();
	}

	/*
	 <title>用這個慶祝紀念日或開趴時招待客人♪利用牛奶盒做的♪莓果冰淇淋蛋糕</title>
	 <publishTimestamp>1494208803</publishTimestamp>
	 <category>料理</category>
	 <imageUrl>
	 https://ccs3.akamaized.net/cchanclips/b00b19115237458aa36b6fe24f802681/th_w320_0006.png
	 </imageUrl>
	 <content>
	 <![CDATA[
	 by C CHANNEL Food
	 　
	 看起來超專業的冰淇淋蛋糕，可以用牛奶盒做出來唷！
	 莓果的酸、蛋白霜的甜，實在是非常相得益彰♪
	 特別日子想要慶祝，或是要在家招待客人，自己動手做一個試試看吧♡
	 或是妳有更好的配方，也請告訴我們唷♪

	 ■材料
	 冷凍綜合莓果(打成泥用)：150g
	 檸檬汁：2小匙
	 鮮奶油：200ml
	 蛋白：2個分
	 砂糖：80g
	 冷凍綜合莓果（中間內餡用）：100g
	 　
	 （裝飾）
	 打發鮮奶油：適量
	 草莓：適量
	 藍莓：適量
	 蜂蜜：適量
	 薄荷：適量
	 　
	 ■事前準備
	 冷凍綜合莓果解凍後備用。
	 裝飾用的草莓對半切開，和藍莓一起均勻地裹上蜂蜜。
	 　
	 ■作法
	 1.牛奶盒的其中1面剪掉，開口處用膠帶固定。冷凍綜合莓果、檸檬汁放入食物處理機中打成泥狀，倒入碗中備用。2.同一個食物處理機中放入鮮奶油，打成糊狀後倒入步驟1，用橡皮刮刀攪拌均勻。
	 製作蛋白霜，將蛋白放入另一大碗，分4次加入砂糖，用手持攪拌器打到將攪拌器拿起來時，蛋白會呈一個角。
	 3.將蛋白霜倒入步驟2的碗中，用橡皮刮刀以切的方式攪拌均勻，接著放入內餡用的冷凍綜合莓果，稍微拌勻。4.倒入牛奶盒中，送入冷凍庫冰到變硬，取出後去除牛奶盒，上方用打發鮮奶油、草莓、莓果、薄荷裝飾，就完成了♪"
	 ]]>
	 </content>
	 <videoScript>
	 <![CDATA[
	 <div id="area"></div><script type="text/javascript" src="https://www.cchan.tv/show/b00b19115237458aa36b6fe24f802681?pid=0070&disp_area=area&autoplay=true&muted=true&loop=true&controls=true&playsinline=true" async="async"></script>
	 ]]>
	 </videoScript>
	 <videoID>b00b19115237458aa36b6fe24f802681</videoID>
	 <sourceUrl>
	 https://www.cchan.tv/watch/b00b19115237458aa36b6fe24f802681
	 </sourceUrl>
	 <author>C CHANNEL</author>
	 </article>
	 */

	public function importFeed() {
		$url='http://gotv.ctitv.com.tw/category/cchannel/feed';
		$content=file_get_contents($url);
		$xml=simplexml_load_string($content,"SimpleXMLElement",LIBXML_NOCDATA);
		foreach($xml as $a) {
			$a->category=$a->category;
			$a->videoID=$a->asf;
			$a->publishTimestamp=$a->pubDate;
			$a->title=$a->title;
			$a->content=$a->description;
			//$a->imageUrl=;
			$xml->article[]=$a;
		}
		$xml->ID=date('Y-m-d');
//		print_r($xml);
	}

	public function handle() {
		//$this->importFeed();
		//return;
		$total=0;
		$url=env('CCHANEL_URL');
		$this->info('chanel get url='.$url);
		$content=file_get_contents($url);
		$xml=simplexml_load_string($content,"SimpleXMLElement",LIBXML_NOCDATA);
		$pId='58f0700e2fc1bb209358dde2';
		$pModel=ProgramModel::where('_id','=',$pId)->first();
		$date=date('Y-m-d',strtotime($xml->ID));
		$this->info('date='.$date);
		if(count($pModel)) {
			foreach($xml->article as $a) {
				$vcModel=VideoClassModel::where('name','=',(string)$a->category)->first();
				if(count($vcModel)) {
					$id=(string)$a->videoID;
					$createdAt=date('Y-m-d H:i:s',(string)$a->publishTimestamp);
					$count=VideoModel::where('yt_id','=',$id)->count();
					if($count) {
						continue;
					}
					$vModel=new VideoModel();
					$vModel->program=$pModel->_id;
					$vModel->title=(string)$a->title;
					$vModel->video_class=$vcModel->_id;
					$vModel->short_title=(string)$a->title;
					$vModel->created_at=$createdAt;
					$vModel->hits_plus=0;
					$vModel->hits=0;
					$vModel->views=0;
					$vModel->status=1;
					$vModel->date=$date;
					$vModel->summary=(string)$a->content;
					$vModel->article=(string)$a->content;
					$vModel->episode=0;
					$vModel->small_pic=(string)$a->imageUrl;
					$vModel->yt_id=(string)$a->videoID;
					$vModel->type=1;
					$vModel->sort=100;
					$vModel->length=0;
					$vModel->save();
					Log::info('add cchanel video title='.(string)$a->title.' id='.$vModel->_id);
					$total+=1;
				}
			}
		}
		$this->info('total='.$total);
	}

}
