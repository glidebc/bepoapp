<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">

		<!-- Sidebar Menu -->
		<ul class="sidebar-menu">
			<!--
			<li class="header">HEADER</li>
			-->
			<!-- Optionally, you can add icons to the links -->
			@inject('user','App\User')
			<?php
			$progs=$user->progs()->where('users.id',Auth::user()->id)->get();
			$menutree=array();
			$title='主控台';
			foreach($progs as $prog) {
				if($prog['path']==Request::segment(1)) {
					$prog['active']='active';
					$title=$prog['name'];
					$menutree[$prog['menuid']]['active']='active';
				}
				$menutree[$prog['menuid']]['progs'][]=$prog;
				$menutree[$prog['menuid']]['title']=$prog['menutitle'];
			}
			?>
			@section('title',$title)
			@foreach($menutree as $idx=>$menu)
			<li class="treeview {{$menu['active'] or ''}}">
				<a href="#"><i class='fa fa-folder'></i> <span>{{$menu['title']}}</span> <i class="fa fa-angle-left pull-right"></i></a>
				<ul class="treeview-menu">
					@foreach($menu['progs'] as $prog)
					<li class="{{ $prog['path']==Request::segment(1) ? 'active' : '' }}">
						<a href="{{url($prog['path'])}}"> <i class="fa fa-circle-o"></i>{{$prog['name']}}</a>
					</li>
					@endforeach
				</ul>
			</li>
			@endforeach
		</ul><!-- /.sidebar-menu -->
	</section>
	<!-- /.sidebar -->
</aside>
