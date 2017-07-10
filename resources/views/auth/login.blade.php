@extends('layouts.auth')

@section('htmlheader_title')
Log in
@endsection

@section('content')
<body class="hold-transition login-page">
    <div class="login-box"  >
        <div class="login-logo" style="border-top-left-radius: 10px;
        border-top-right-radius: 10px;background-color: #fff;margin-bottom:0;padding-top:10px;">
            <a href="{{ url('/home') }}"><img style="width:80%" src="{{asset('/img/logo-header1.png')}}"></a>
        </div><!-- /.login-logo -->

        @if (count($errors) > 0)
        <div class="alert alert-danger" style="margin-bottom:0;">
            <ul>
                @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="login-box-body" style="
        border-bottom-right-radius: 10px;
        border-bottom-left-radius: 10px;">
            <form action="{{ url('/login') }}" id='login-form' method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" id="email" class="form-control" placeholder="Email"  autocapitalize="off" autocomplete="off" name="email"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password"  autocapitalize="off" autocomplete="off" name="password"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        @if(false)
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember">
                                Remember Me </label>
                        </div>
                        @endif
                    </div><!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">
                            Sign In
                        </button>
                    </div><!-- /.col -->
                </div>
            </form>

            <!--
            <a href="{{ url('/password/reset') }}">I forgot my password</a><br>
            <a href="{{ url('/register') }}" class="text-center">Register a new membership</a>
            -->
        </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->
    @include('layouts.partials.scripts_auth')
    <script>
		$(function() {
			$('#email').val($.cookie('email'));
			$('input').iCheck({
				checkboxClass : 'icheckbox_square-blue',
				radioClass : 'iradio_square-blue',
				increaseArea : '20%' // optional
			});
			$('#login-form').bind('submit', function() {
				var email = '';
				if ($.cookie('email')) {
					email = $.cookie('email');
				} else {
					email = $('#email').val();
				}
				$.cookie('email', email);
				return true;
			});
		});
    </script>
</body>
@endsection
