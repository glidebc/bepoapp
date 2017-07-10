<!DOCTYPE html>
<html lang="en">
	@section('htmlheader')
	@include('gotv.layouts.partials.htmlheader')
	@show
	<body class="skin-blue sidebar-mini">
		<div class="wrapper" >
			@include('gotv.layouts.partials.mainheader')
			<div class="content-wrapper">
				@include('gotv.layouts.partials.contentheader')
				<section class="content" style="min-height:800px;">
					@yield('main-content')
				</section>
			</div>@include('gotv.layouts.partials.footer')
		</div>
		@section('scripts')
		@include('gotv.layouts.partials.scripts')
		@show
	</body>
</html>
