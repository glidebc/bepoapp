<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="{{asset('css/ad.css')}}" rel="stylesheet" type="text/css" />
		<!--
		<script src="{{asset('js/jquery.min.js')}}" type="text/javascript"></script>
		-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
		<title>{{$data->title}}</title>
	</head>
	<body>
		<div id='wrap'>
			<div id='container'>
			    @if($data->type=='1'||$data->type=='2')
				<div class='videoWrapper'>
					<iframe width='320' height='180' src="https://www.youtube.com/embed/{{$data->youtube_id}}" frameborder='0' allowfullscreen></iframe>
				</div>
				@endif
				@if($data->type=='0'||$data->type=='2')
				<div class='story'>
					<img id='youtube-thum' src='{{asset('ad_images/'.$data->image_url)}}' width='100%' alt=''/>
				</div>
				@endif
			</div>
		</div>
		<!-- embed begin-->
		{!!$data->embed!!}
		<!-- embed end -->
		<script type='text/javascript'>
            $(function(){
                $('#youtube-thum').on('click',function(e){
                    $.ajax( '{!!url('services/ad_update').'?id='.$data->id.'&'.'from='.$data->from!!}' )
                      .done(function() {
                          window.location.href='{{$data->click_url}}';
                      })
                      .fail(function() {
                      })
                      .always(function() {
                      });
                    
                });
            });
        </script>
	</body>
</html>
