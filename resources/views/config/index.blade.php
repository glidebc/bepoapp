@extends('layouts.app')

@section('htmlheader_title')
Cfgs
@endsection

@section('main-content')
<style>
	.list-edit {
		background-color: #fff;
		padding: 10px;
	}
	.list-edit div.row {
		margin-bottom: 3px;
	}
	.fa {
	}
	.fa-times {
		color: #f39c12;
	}
</style>
@if(isset($messages))
@foreach($messages as $message)
<div class="bg-success text-center">
	{{$message}}
</div>
@endforeach
@endif
<div class="rpd-dataform list-edit">
	<?php
	$isArray=is_array($data);
	$list=$isArray?$data:array($data);
	//$list=count($data)>0?$list:array();
	?>
	<div class="row">
		<div class="col-sm-12" >
			@foreach($cfgs as $key=>$values)
			<a style="margin:3px;font-weight:900;" class="btn {{Request::segment(3)==$key?'btn-warning':'btn-default' }}" href="{{url(  Request::segment(1).'/index/'.$key)}}">{{str_replace(':','_',$key)}}</a>
			@endforeach
		</div>
	</div>
	@if(Request::segment(3))
	<form method="get" action="">
		<script type="text/html" id="template">
			<div class="row">
			<div class="col-sm-3" >
			<input class="form-control" type="text" name="keys[]" value="">
			</div>
			<div class="col-sm-6" >
			<div style="float:left;width:95%;">
			<input class="form-control" type="text" name="values[]" value="">
			</div>
			<div style="float:right;">
			<a href="javascript:" class="btn-cfg-delete"><i class="fa fa-times" aria-hidden="true"></i></a>
			</div>
			</div>
			</div>
		</script>
		<hr>
		<div id="main-grid">
			<input name="key" value="{{Request::segment(3)}}" type="hidden">
			<input name="is_array" value="{{$isArray}}" type="hidden">
			@foreach($list as $k=>$v)
			<div class="row">
				@if($isArray)
				<div class="col-sm-3" >
					<input class="form-control" type="text" name="keys[]" value="{{$k}}">
				</div>
				@endif
				<div class="col-sm-6" >
					<div style="float:left;width:95%;">
						<input class="form-control" type="text" name="values[]" value="{{$v}}">
					</div>
					<div style="float:right;">
						<a href="javascript:;" class="btn-cfg-delete"><i class="fa fa-times" aria-hidden="true"></i></a>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		<input type="submit" name="do" value="更新" class="btn-primary btn">
		@if($isArray)
		<input type="button" name="do" class="btn-cfg-add btn btn-success" value="新增">
		@endif
	</form>
	@endif
</div>
@endsection
@section('scripts')
<script type="text/javascript">
	$(function() {
		var templ = $('#template').html();
		var grid = $('#main-grid ');
		$('.btn-cfg-add').on('click', function(evt) {
			console.log('add');
			grid.append(templ);
		});
		$('#main-grid').on('click', function(evt) {
			console.log(evt.target);
			var isDelete = $(evt.target).parent().hasClass('btn-cfg-delete');
			console.log(isDelete);
			if (isDelete) {
				$(evt.target).parent().parent().parent().parent().remove();
			}
			//$(this).parent().parent().parent().remove();
		});
	}); 
</script>
@endsection