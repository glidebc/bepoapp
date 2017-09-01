@extends('layouts.app')

@section('htmlheader_title')
Cfgs
@endsection

@section('main-content')
<form action=""  method="post">
	<div class="row">
		<div class="col-xs-4">
			{{ csrf_field() }}
			<input type="submit" value="刪除" class="btn btn-warning" name="do">
		</div>
		<div class="col-xs-8 text-right">
			總計{{count($datalist)}}筆
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th class="col-md-1"><label>
							<input type="checkbox" id="select-all">
							全選/取消</label></th>
						<th class="col-md-1"><label>序號</label></th>
						<th class="col-md-3"><label>連結</label></th>
						<th><label>描述</label></th>
						<th><label>建立時間</label></th>
					</tr>
				</thead>
				<tbody>
					@foreach($datalist as $data)
					<tr>
						<td>
						<input type="checkbox" name="ids[{{$data->id}}]">
						</td>
						<td>{{$data->id}}</td>
						<td>{{$data->url}}</td>
						<td>{{$data->description}}</td>
						<td>{{$data->created_at}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</form>
@endsection
@section('scripts')
<script type="text/javascript">
	$(function() {
		$('#select-all').on('click', function() {
			var ids = $('input[name^=ids]');
			ids.prop('checked', $(this).prop('checked'));
		});
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