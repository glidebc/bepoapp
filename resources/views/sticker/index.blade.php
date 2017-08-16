<!DOCTYPE html>
<html lang="en">
	<head>
		<style>
			.travel-box {
				width: 300px;
				margin:0
				padding:8px;
				border:1px solid #ccc;
			}
			.travel-box .trvael-entry {
				clear: both;
				overflow:hidden;
			}
			.travel-box .trvael-entry .entry-left {
				float: left;
				width: 35%;
			}
			.travel-box .trvael-entry .entry-right {
				float: left;
				width: 65%;
			}
		</style>

	</head>
	<body>
		<div class="travel-box">
			@foreach($datalist as $data)
			<div class="trvael-entry">
				<div class="entry-left">
					<a target="_blank" href="{{$data['url']}}" title="{{$data['name']}}"><img src="{{$data['pic']}}"></a>
				</div>
				<div class="entry-right">
					@foreach($data['entries'] as $e)
					<div>
						<a target="_blank" href="{{$e['url']}}">{{$e['title']}}</a>
					</div>
					@endforeach
				</div>
			</div>
			@endforeach
		</div>
	</body>
</html>