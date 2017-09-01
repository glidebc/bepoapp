<!DOCTYPE html>
<html lang="zh-TW">
    <title>{{config('global.site_name')}}-{{$site_title}}</title>
    <link rel="shortcut icon" href="favicon.png" />
    @include('layouts.partials.styles')
    @show
    @include('layouts.partials.scripts')
    {!! Rapyd::head() !!}
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>


    <link media="all" type="text/css" rel="stylesheet" href="{{asset('datetimepicker/bootstrap-datetimepicker.min.css')}}">
    <script src="{{asset('datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('datetimepicker/bootstrap-datetimepicker.zh-TW.js')}}"></script>


    <body class="skin-blue sidebar-mini" style='font-family:"Helvetica", Verdana, Arial, sans-serif;color:#222;'>
        <div class="wrapper">
            @include('layouts.partials.mainheader')
            @include('layouts.partials.sidebar')
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                @include('layouts.partials.contentheader')
                <!-- Main content -->
                <section class="content" style="padding-top:3px;">
                    <!-- Your Page Content Here -->
                    @yield('main-content')
                </section><!-- /.content -->
            </div><!-- /.content-wrapper -->
            @include('layouts.partials.controlsidebar')
            @include('layouts.partials.footer')
        </div><!-- ./wrapper -->
        <div id="footer"></div>
        @show
    </body>
</html>
