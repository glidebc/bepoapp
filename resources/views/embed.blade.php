<!DOCTYPE html>
<html lang="en">
	<title>今日最夯新聞</title>
	@section('htmlheader')
	@include('layouts.partials.htmlheader')
	@show
	<style>
		.embed-item{
			overflow:hidden;
			max-width:280px;
			margin-bottom:5px;
			border:1px solid #ccc;
			padding:3px;
			
		}
		.embed-item .embed-header {
		}
		.embed-item .embed-body {
			overflow:hidden;
		}
		.embed-item .embed-body a{
			color:#F06624;
			font-size:13px;
			font-weight: bold;
			line-height:20px;
		}
		.embed-item .embed-header img{
			max-width: 100%;
   			height: auto;
		}
		
		.embed-title h3{
			font-size:15px;
			font-weight: bold;
			cursor: auto;
		}
    
	</style>
	<head>
		<div>
			<div class="embed-title">
			<h3>最夯新聞</h3>
			</div>
			<?php foreach($datalist as $data):
			?>
			<a target="_blank" href="<?php echo $data['htm_url']?>?src=bao">
			<div class="embed-item">
				<div class="embed-header">
					<img src="<?php echo $data['image_thum']?>">
				</div>
				<div class="embed-body">
					<?php echo $data['title']?> <?php if(false):?><i class="fa fa-external-link" aria-hidden="true"></i><?php endif;?>
				</div>
			</div>
			</a>
			<?php endforeach; ?>
		</div>
	</head>
</html>
