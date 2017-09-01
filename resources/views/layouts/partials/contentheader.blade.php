<!-- Content Header (Page header) -->
<section class="content-header">
	<!--
    <h1>
        @yield('contentheader_title', 'Page Header here')
        <small>@yield('contentheader_description')</small>
    </h1>
  -->
   <ol class="breadcrumb" style="float:none;position:static;">
	        <li><a href="/home"><i class="fa fa-dashboard"></i>主控台</a></li>
	        @inject('prog','App\Progs')
	        <?php
	        	$proginfo=$prog->join('menus','progs.menu_id','=','menus.id')->where('path','=',Request::segment(1))->first();
	        ?>
	        @if($proginfo)
	        <li class="active">{{$proginfo->title}}</li>
	        <li class="active">{{$proginfo->name}}</li>
			@endif    
	</ol>
</section>