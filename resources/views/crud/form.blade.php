@extends('crud.app')
@section('main-content')
<style>
	div.form-group {
		margin-bottom: 10px;
	}
	div.form-group  label {
		font-weight: bold;
		font-size: 1.1em;
		color: #000;
	}
	div.form-group div.col-sm-9 label {
		display: none;
	}
</style>
<script type="text/javascript">
$(function(){
    if(false){
        $('.error-block').show(0).delay(3000).hide(0);
    }
});
</script>
<div id="" class="rpd-dataform">
    @if($form->model->error)
    <div class="row error-block">
        <div class="col-sm-12">
          <p class="text-center alert alert-danger">{{$form->model->error}}</p>    
        </div>
    </div>
    @endif
    @if (session('message'))
    <div class="row">
         <div class="col-sm-12">
            <p class="text-center alert alert-success">{{ session('message') }}</p>    
        </div>
    </div>
    @endif
	<div class="row">
		<div class="col-sm-8">
			{!! $form->header !!}
			{!! $form->message !!}
			<br />
			@if(!$form->message)
			@foreach ($form->fields as $field)
			<div class="form-group clearfix" id="fg_{{$field->name}}">
				<label for="{{$field->name}}" class="col-sm-3 control-label required">{!!$field->label!!}@if($field->required)<i class="fa fa-asterisk" style="font-size:0.5em;text-shadow: 1px 1px 1px #ccc;color:#ff0000;" aria-hidden="true"></i>@endif</label>
				<div class="col-sm-9" id="div_{{$field->name}}">
					{!!$form->render($field->name)!!}
				</div>
			</div>
			@endforeach
			@endif
			{!! $form->footer !!}
		</div>
	</div>
</div>
@endsection