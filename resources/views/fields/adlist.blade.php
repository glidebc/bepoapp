<table class="table table-striped">
@foreach($datalist as $idx=>$data)
<?php $t=($idx==0)?'th':'td';?>
	<tr>
	@foreach($data as $value)
		<{{$t}}>
		{{$value}}
		</{{$t}}>
	@endforeach
	<tr>
@endforeach
</table>
