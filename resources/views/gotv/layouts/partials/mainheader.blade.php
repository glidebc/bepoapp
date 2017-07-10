<!-- Main Header -->
<header class="main-header">

	<!-- Logo -->
	<a href="{{ url('/home') }}" class="logo"> <!-- mini logo for sidebar mini 50x50 pixels --> <span class="logo-mini"><b>A</b>LT</span> <!-- logo for regular state and mobile devices --> <span class="logo-lg"> <img style=" width: auto;
		height : auto;
		max-height: 60%;
		max-width: 60%;;padding-bottom:12px;margin-right:-15px;" src="{{ asset('/img/logo-header1.png') }}"> <b>Admin</b> </span> </a>

	<!-- Header Navbar -->
	<nav class="navbar navbar-static-top" role="navigation">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"> <span class="sr-only">Toggle navigation</span> </a>
		<!-- Navbar Right Menu -->
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				@if (Auth::guest())
				<li>
					<a href="{{ url('/login') }}">Login</a>
				</li>
				<li>
					<a href="{{ url('/register') }}">Register</a>
				</li>
				@else
				<!-- User Account Menu -->
				<li class="dropdown user user-menu">
					<!-- Menu Toggle Button -->
					<ul class="my-profile">
						<li>
							<div class="glyphicon glyphicon-user"></div>
					{{ Auth::user()->name }}
					</li>
					<li>
					<a class="glyphicon glyphicon-log-out" href="{{url('/logout')}}"></a>
					</li>
					</ul>
				</li>
				@endif

			</ul>
		</div>
		<style>
			.my-profile{
				overflow:hidden;
				clear:both;
				color:#FFF;
				margin:15px 0;
			}
			.my-profile li{
				display:block;
				font-size:14px;
				font-weight:900;
				float:left;
				margin-right:10px;
			}
			.my-profile a{
				color:#FFF;
			}
		</style>
	</nav>
</header>