<?php

namespace App\Http\Controllers\Bepoapp;
use Illuminate\Http\Request;
use App\Http\Requests;
use Zofe\Rapyd\Facades\DataSet;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataForm;
use Zofe\Rapyd\Facades\DataEdit;
use Zofe\Rapyd\Facades\DataFilter;
use App\Bepoapp\NewEvent;
use DB;
use Input;

class NewEventController extends Controller
{
    public function getIndex() {
		$source=new NewEvent();
        $filter=DataFilter::source($source);
        $filter->add('title','名稱','text');
		$filter -> add('status', '狀態', 'select') -> options(config('global.post_status_all'));
        $filter->submit('搜尋');
        $filter->reset('重置');
		$filter -> build();
        $grid=DataGrid::source($filter);
        $grid->attributes(array("class"=>"table table-striped"));
        $grid->add('title','標題',true)-> cell(function($value, $row) {
        	$id=$row['id'];
			$url='http://bepo.ctitv.com.tw/events/index.php?id='.$id.'&mid=1f&po=ctitoken';
            return "<a target='_blank' title='$value' href='$url'>$value</a>";
        }) ->style('width:20%');
		$grid->add('thumb_url','圖片',true)-> cell(function($value, $row) {
			return '<img style="width:90%;" src="'.env('APP_URL').'/event_thumbs/'
			.$value.'"';
		}) ->style('width:20%');
		$grid->add('bg_img_url','背景圖',true)-> cell(function($value, $row) {
			return '<img style="width:80%;" src="'.env('APP_URL').'/event_bg_images/'
			.$value.'"';
		}) ->style('width:20%');
		$grid -> add('status', '狀態', true) -> cell(function($value, $row) {
			return config('global.post_status_all')[$value];
		}) -> style('width:10%');

		$grid -> add('all_count', '參與人數',false) -> cell(function($value, $row) {
			$count=DB::connection('mysql')->table('bepo.newevents')
				->where('event_id',$row['id'])
				->count();
			return $count;
		}) -> style('width:10%');
		$grid->add('bgcolor','背景顏色',true)-> cell(function($value, $row) {
			return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
		}) -> style('width:10%');
		$grid->add('fontcolor','字體顏色',true)-> cell(function($value, $row) {
			return "<div style='border:1px solid #CCC;background-color:$value;'>&nbsp;</div>";
		}) -> style('width:10%');
		$grid->add('priority','排序',true)->style('width:5%');
		$grid->add('lottery','中獎清單',false)-> cell(function($value, $row) {
			$id='lottery-'.$row['id'];
			$btnid='lottery-'.$row['id'].'-btn';
			$i=$row['id'];
			$html="
				<script type='text/javascript'>
					$(function(){
							$('#$btnid').on('click',function(){
								var count=$('#$id').val();
								if(count<1){
									alert('請填入有效抽獎數量!');
								}else{
									var url='beponewevent/lottery?count='+count+'&id='+$i;
									//window.open(url,'_blank');
									window.location.href=url;
								}
							});
					});
				</script>
			";
			$html.="<input size='15' type='text' placeholder='填入抽獎數量' value='' id='$id' >";
			return $html.="<a id='$btnid' href='javascript:void();'>產生清單</a>";
        }) ;
        $grid->edit('beponewevent/edit','操作','show|modify|delete')->style('width:12%');
        $grid->link('beponewevent/edit',"新增","TR");
        $grid->orderBy('priority','asc');
        $grid->paginate(config('global.rows_of_page'));
        $grid -> build('crud.datagrid');
		return view('crud.grid', compact('filter', 'grid'));
    }
	public function anyLottery() {
		$count=Input::get('count');
		$id=Input::get('id');

		if(empty($count))return;
		if(empty($id))return;

		$info=NewEvent::find($id);
		if(count($info)<1)return;
		$date=date('Y-m-d H:i:s');

		$content='';
		$filename='';
		if(true){
			$users=DB::connection('mysql')->table('bepo.newevents')
			->where('event_id',$id)
			->orderBy(\DB::raw('rand()'))->take($count)->get();
			$total=count($users);
			$content=$info->title." 中獎名單 $total/$count 名\n\n";
			$filename .= $info->title." 中獎名單 $total/$count 名 ".$date;
			$seg1='';
			$seg2='';
			foreach($users as $user){
				$name=trim($user->name);
				$tel=trim($user->tel);
				$address=trim($user->address);
				$sn=trim($user->sn);

				$seg2.=$name.' '.$tel.' '.$address.' '.$sn."\n";
				$name=mb_substr($name,0,1,"utf-8").'x'.mb_substr($name,1,-1,"utf-8");
				$tel=mb_substr($tel,0,4,"utf-8").'xxxxx'.mb_substr($tel,8,-1,"utf-8");
				$address=mb_substr($address,0,3,"utf-8").'xxxxxxxxxxxxxxxxx'.mb_substr($address,-5,-1,"utf-8");
				$seg1.=$name.' '.$tel.' '.$address.' '.$sn."\n";
			}
			$content.=$seg1."\n\n".$seg2;
		}else{
			$filename.='查無活動資料';
			$content.='查無活動資料';
		}

        $headers = ['Content-type'=>'text/plain'
        		,'Content-Disposition'=>sprintf('attachment; filename="%s"', $filename)
        		,'Content-Length'=>strlen($content)];
        return response($content, 200, $headers);
	}
    public function anyEdit() {
        $edit=DataEdit::source(new NewEvent());
        $edit->link("beponewevent","回列表","TR")->back();
        $edit->add('title','標題','text')
            -> rule('required|unique:bepoapp_star,title,' . $edit -> model -> id)
            -> updateValue($edit -> model -> title);
		$edit -> add('status', '狀態', 'select') -> options(config('global.post_status'));
		$edit->add('start_time','開始時間','datetime')-> rule('required');
		$edit->add('end_time','結束時間','datetime')-> rule('required');
        $edit -> add('description', '描述', 'App\Fields\Tinymce') -> attr('rows', 25) -> rule('required');
		//$edit->add('tb','活動資料表','text')-> rule('required');
		$edit->add('thumb_url','縮圖', 'image')
		     ->move('event_thumbs/')
             ->resize(600,320)
             ->preview(300,160)-> rule('required');
		$edit->add('bg_img_url','背景圖', 'image')
		     ->move('event_bg_images/')
             ->resize(640,630,'bg_'.md5(time()))
             ->preview(320,315)-> rule('required');
        $edit->add('priority','排序','number')->insertValue(10)-> rule('required');
		//$edit->add('url','連結','text')-> rule('required');
		$edit->add('bgcolor','背景顏色','colorpicker')-> rule('required');
		$edit->add('fontcolor','字體顏色','colorpicker')-> rule('required');
        return $edit->view('crud.edit',compact('edit'));
    }
}
