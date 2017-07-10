@extends('layouts.app')

@section('htmlheader_title')
Home
@endsection

@section('main-content')
<section class="content">
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">資料列表</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="dataTables_wrapper form-inline dt-bootstrap">
						<div class="row">
							<div class="col-sm-6"></div><div class="col-sm-6"></div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<table id="" class="table table-bordered table-hover dataTable" role="grid" >
									<thead>
										<tr role="row">
											<th tabindex="0"  rowspan="1" colspan="1" aria-sort="ascending" >系統編號</th>
											<th tabindex="1"  rowspan="1" colspan="1" >系統名稱</th>
											<th tabindex="2"  rowspan="1" colspan="1" >系統路徑</th>
											<th tabindex="4"  rowspan="1" colspan="1" >排序值</th>
											<th tabindex="5"  rowspan="1" colspan="1" >啟用狀態</th>
											<th tabindex="6"  rowspan="1" colspan="1" >操作</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($data_list as $idx=> $user)
										<tr role="row" class="{{($idx+1)%2>0?'odd':'even'}}">
											<td><a href='{{url('posts/edit/0')}}'>{{$user->id}}</a></td>
											<td>{{$user->name}}</td>
											<td>{{$user->path}}</td>
											<td>{{$user->priority}}</td>
											<td>{{$user->enabled}}</td>
                                            <td><button type="button" class="btn btn-block btn-danger btn-sm">刪除</button></td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-5">
								<div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
									total : {{$data_list->total()}} rows
								</div>
							</div>
							<div class="col-sm-7">
								{{$data_list->render()}}
							</div>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
			</div>

		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
	<div style="padding: 10px 0px; text-align: center;">
		<div class="visible-xs visible-sm">
			<!-- AdminLTE --><ins class="adsbygoogle" style="display: inline-block; width: 300px; height: 250px;" data-ad-client="ca-pub-4495360934352473" data-ad-slot="5866534244" data-adsbygoogle-status="done"><ins id="aswift_0_expand" style="display:inline-table;border:none;height:250px;margin:0;padding:0;position:relative;visibility:visible;width:300px;background-color:transparent"><ins id="aswift_0_anchor" style="display:block;border:none;height:250px;margin:0;padding:0;position:relative;visibility:visible;width:300px;background-color:transparent"><iframe width="300" height="250" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" scrolling="no" allowfullscreen="true" onload="var i=this.id,s=window.google_iframe_oncopy,H=s&amp;&amp;s.handlers,h=H&amp;&amp;H[i],w=this.contentWindow,d;try{d=w.document}catch(e){}if(h&amp;&amp;d&amp;&amp;(!d.body||!d.body.firstChild)){if(h.call){setTimeout(h,0)}else if(h.match){try{h=s.upd(h,i)}catch(e){}w.location.replace(h)}}" id="aswift_0" name="aswift_0" style="left:0;position:absolute;top:0;"></iframe></ins></ins></ins>
			<script>
				( adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
		<div class="hidden-xs hidden-sm">
			<!-- Home large leaderboard --><ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-4495360934352473" data-ad-slot="1170479443" data-adsbygoogle-status="done"><ins id="aswift_1_expand" style="display:inline-table;border:none;height:90px;margin:0;padding:0;position:relative;visibility:visible;width:728px;background-color:transparent"><ins id="aswift_1_anchor" style="display:block;border:none;height:90px;margin:0;padding:0;position:relative;visibility:visible;width:728px;background-color:transparent"><iframe width="728" height="90" frameborder="0" marginwidth="0" marginheight="0" vspace="0" hspace="0" allowtransparency="true" scrolling="no" allowfullscreen="true" onload="var i=this.id,s=window.google_iframe_oncopy,H=s&amp;&amp;s.handlers,h=H&amp;&amp;H[i],w=this.contentWindow,d;try{d=w.document}catch(e){}if(h&amp;&amp;d&amp;&amp;(!d.body||!d.body.firstChild)){if(h.call){setTimeout(h,0)}else if(h.match){try{h=s.upd(h,i)}catch(e){}w.location.replace(h)}}" id="aswift_1" name="aswift_1" style="left:0;position:absolute;top:0;"></iframe></ins></ins></ins>
			<script>
				( adsbygoogle = window.adsbygoogle || []).push({});
			</script>
		</div>
	</div>
</section>
@endsection
