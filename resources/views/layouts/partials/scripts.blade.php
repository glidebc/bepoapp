<script src="{{ asset('/js/jquery.min.js') }}"></script>
<script src="{{ asset('/js/jquery.cookie.js') }}"></script>
<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/jquery.pjax.js') }}"></script>
<script src="{{ asset('/js/riot+compiler.min.js') }}"></script>
<script src="{{ asset('/js/app.min.js') }}"></script>
<script src="{{ asset('/tinymce/jquery.tinymce.min.js') }}"></script>
<script src="{{ asset('/js/copyto.js') }}"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js" type="text/javascript" charset="utf-8"></script>
<script src="{{ asset('/js/tag-it.min.js') }}"></script>
@if(true)
<script src="{{ asset('/tinymce/tinymce.min.js') }}"></script>
@endif



<script src="{{ asset('/js/jquery.bootstrap.newsbox.min.js')}}" type="text/javascript"></script>
<script type="text/javascript">riot.mount('*')</script>
<script type="text/javascript">
	tinymce.init({
		external_plugins: { "filemanager" : "<?php echo env('APP_URL')?>/filemanager/plugin.min.js"}
	});
	$(function() {
		$(".mynewsbox").bootstrapNews({
			newsPerPage : 10,
			autoplay : true,
			pauseOnHover : true,
			direction : 'up',
			newsTickerInterval : 4000,
			onToDo : function() {
			}
		});
		
		$('.sidebar-toggle').on('click',function(){
			var is_collapse=$('body').hasClass('sidebar-collapse');
			$.cookie("is_collapse", is_collapse);
		});
		
	});
	
</script>
