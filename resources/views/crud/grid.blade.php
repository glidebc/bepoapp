@extends('crud.app')
@section('main-content')
<div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
<div class="row">
	<div class="col-sm-12">
	    @if($filter)
	    <h3>條件過濾：</h3>
		{!! $filter !!}
		@endif
		{!! $grid !!}
	</div>
</div>
</div>
@endsection
