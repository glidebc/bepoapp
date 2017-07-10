@extends('layouts.app')

@section('htmlheader_title')
Home
@endsection

@section('main-content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Global information</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>{{$posts_count_today}}</h3>
                                    <p>
                                        本日文章數
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>{{$posts_count_month}}</h3>
                                    <p>
                                        本月文章數
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>{{$posts_count}}</h3>
                                    <p>
                                        總文章數
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                            </div>
                        </div>
                        <!-- ./col -->
                        <?php if(false):?>
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>{{$posts_hits_today}}</h3>

                                    <p>
                                        本日文章點擊數
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                            </div>
                        </div>
                        <?php endif;?>
                        <!-- ./col -->
                    </div>
                    
                    
                    
                    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  					<div class="row">
  						<div class="col-lg-12">
  						<div id="chart_div"></div>
  						</div>
  						<script type="text/javascript">
  							google.charts.load('current', {packages: ['corechart', 'line']});
							google.charts.setOnLoadCallback(drawBasic);
							
							function drawBasic() {
							
							      var data = new google.visualization.DataTable();
							      data.addColumn('string', '日期');
							      data.addColumn('number', '文章數');
							      data.addRows(<?php echo json_encode($daily_post_count)?>);
							      var options = {
							        hAxis: {
							          title: '日期'
							        },
							        vAxis: {
							          title: '文章數',
							          baseline:0,
							          showTextEvery:2
							        },
							        
							      };
							      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
							      chart.draw(data, options);
							    }
  						</script>
  					</div>
  					
                    
                    <div class="row">
						<?php if(false):?>
                        <div class="col-xs-6">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <span class="glyphicon glyphicon-list-alt"></span><b>公告</b>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <ul class="mynewsbox" style="overflow-y: hidden; height: 210px;">
                                                @inject('anno','App\Announcements')
                                                <?php $anno=$anno->orderBy('created_at','desc')->take(30)->get()?>
                                                @foreach($anno as $an)
                                                <li class="news-item" style="display: list-item;">
                                                    <table cellpadding="4">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                <!--
                                                                	<img src="images/4.png" width="60" class="img-circle">
                                                                -->
                                                                	{{App\User::find($an->created_user_id)->name}}
                                                                </td>
                                                                <td>
                                                                    {!!$an->content!!}
                                                                    <div style="opacity: 0.6;font-size:0.9em;">{{$an->created_at}}</div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
						
                        <section class="col-xs-6">
                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="nav-tabs-custom" style="cursor: move;">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right ui-sortable-handle">
                                    <li class="pull-left header">
                                        <i class="fa fa-inbox"></i> 近期文章
                                    </li>
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
                                    <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                        <svg height="300" version="1.1" width="836" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden; position: relative;">
                                            <desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                Created with Raphaël 2.1.0
                                            </desc><defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs>
                                            <text x="49.203125" y="261" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal">
                                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    0
                                                </tspan>
                                            </text><path fill="none" stroke="#aaaaaa" d="M61.703125,261H811" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="49.203125" y="202" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal">
                                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    7,500
                                                </tspan>
                                            </text><path fill="none" stroke="#aaaaaa" d="M61.703125,202H811" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="49.203125" y="143" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal">
                                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    15,000
                                                </tspan>
                                            </text><path fill="none" stroke="#aaaaaa" d="M61.703125,143H811" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="49.203125" y="84.00000000000003" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal">
                                                <tspan dy="4.000000000000028" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    22,500
                                                </tspan>
                                            </text><path fill="none" stroke="#aaaaaa" d="M61.703125,84.00000000000003H811" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="49.203125" y="25.00000000000003" text-anchor="end" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: end; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal">
                                                <tspan dy="4.000000000000028" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    30,000
                                                </tspan>
                                            </text><path fill="none" stroke="#aaaaaa" d="M61.703125,25.00000000000003H811" stroke-width="0.5" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="673.5226875759416" y="273.5" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,7)">
                                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    2013
                                                </tspan>
                                            </text>
                                            <text x="340.29953295868773" y="273.5" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#888888" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 12px; line-height: normal; font-family: sans-serif;" font-size="12px" font-family="sans-serif" font-weight="normal" transform="matrix(1,0,0,1,0,7)">
                                                <tspan dy="4" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    2012
                                                </tspan>
                                            </text><path fill="#74a5c1" stroke="none" d="M61.703125,219.05493333333334C82.64337788578372,219.56626666666668,124.52388365735116,222.62345,145.46413654313488,221.10026666666667C166.4043894289186,219.57708333333335,208.28489520048603,209.1355825136612,229.22514808626974,206.86946666666668C249.93778952764276,204.6279825136612,291.36307241038884,204.88215,312.07571385176186,203.06986666666666C332.7883552931349,201.25758333333332,374.21363817588093,194.9129178506375,394.92627961725395,192.3712C415.86653250303766,189.80155118397084,457.74703827460513,182.51721666666668,478.68729116038884,182.6244C499.62754404617255,182.73158333333333,541.50804981774,204.18057122040074,562.4483027035237,193.22866666666667C583.1609441448968,182.39580455373408,624.5862270276427,101.94395359116024,645.2988684690158,95.48533333333336C665.7838984659782,89.09768692449357,706.7539584599028,135.13802307692308,727.2389884568652,141.8436C748.1792413426489,148.69818974358975,790.0597471142163,147.7554,811,149.726L811,261L61.703125,261Z" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></path><path fill="none" stroke="#3c8dbc" d="M61.703125,219.05493333333334C82.64337788578372,219.56626666666668,124.52388365735116,222.62345,145.46413654313488,221.10026666666667C166.4043894289186,219.57708333333335,208.28489520048603,209.1355825136612,229.22514808626974,206.86946666666668C249.93778952764276,204.6279825136612,291.36307241038884,204.88215,312.07571385176186,203.06986666666666C332.7883552931349,201.25758333333332,374.21363817588093,194.9129178506375,394.92627961725395,192.3712C415.86653250303766,189.80155118397084,457.74703827460513,182.51721666666668,478.68729116038884,182.6244C499.62754404617255,182.73158333333333,541.50804981774,204.18057122040074,562.4483027035237,193.22866666666667C583.1609441448968,182.39580455373408,624.5862270276427,101.94395359116024,645.2988684690158,95.48533333333336C665.7838984659782,89.09768692449357,706.7539584599028,135.13802307692308,727.2389884568652,141.8436C748.1792413426489,148.69818974358975,790.0597471142163,147.7554,811,149.726" stroke-width="3" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><circle cx="61.703125" cy="219.05493333333334" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="145.46413654313488" cy="221.10026666666667" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="229.22514808626974" cy="206.86946666666668" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="312.07571385176186" cy="203.06986666666666" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="394.92627961725395" cy="192.3712" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="478.68729116038884" cy="182.6244" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="562.4483027035237" cy="193.22866666666667" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="645.2988684690158" cy="95.48533333333336" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="727.2389884568652" cy="141.8436" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="811" cy="149.726" r="4" fill="#3c8dbc" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><path fill="#eaf2f5" stroke="none" d="M61.703125,240.02746666666667C82.64337788578372,239.8072,124.52388365735116,241.35496666666666,145.46413654313488,239.1464C166.4043894289186,236.93783333333334,208.28489520048603,223.33676429872497,229.22514808626974,222.35893333333334C249.93778952764276,221.39173096539162,291.36307241038884,233.23263333333333,312.07571385176186,231.36626666666666C332.7883552931349,229.4999,374.21363817588093,209.2890577413479,394.92627961725395,207.428C415.86653250303766,205.54649107468123,457.74703827460513,214.43916666666667,478.68729116038884,216.39600000000002C499.62754404617255,218.35283333333336,541.50804981774,232.37947613843355,562.4483027035237,223.08266666666668C583.1609441448968,213.88690947176687,624.5862270276427,148.2268241252302,645.2988684690158,142.42573333333334C665.7838984659782,136.68839079189686,706.7539584599028,170.47037838827842,727.2389884568652,176.92893333333336C748.1792413426489,183.53101172161175,790.0597471142163,190.23343333333335,811,194.66826666666668L811,261L61.703125,261Z" fill-opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); fill-opacity: 1;"></path><path fill="none" stroke="#a0d0e0" d="M61.703125,240.02746666666667C82.64337788578372,239.8072,124.52388365735116,241.35496666666666,145.46413654313488,239.1464C166.4043894289186,236.93783333333334,208.28489520048603,223.33676429872497,229.22514808626974,222.35893333333334C249.93778952764276,221.39173096539162,291.36307241038884,233.23263333333333,312.07571385176186,231.36626666666666C332.7883552931349,229.4999,374.21363817588093,209.2890577413479,394.92627961725395,207.428C415.86653250303766,205.54649107468123,457.74703827460513,214.43916666666667,478.68729116038884,216.39600000000002C499.62754404617255,218.35283333333336,541.50804981774,232.37947613843355,562.4483027035237,223.08266666666668C583.1609441448968,213.88690947176687,624.5862270276427,148.2268241252302,645.2988684690158,142.42573333333334C665.7838984659782,136.68839079189686,706.7539584599028,170.47037838827842,727.2389884568652,176.92893333333336C748.1792413426489,183.53101172161175,790.0597471142163,190.23343333333335,811,194.66826666666668" stroke-width="3" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><circle cx="61.703125" cy="240.02746666666667" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="145.46413654313488" cy="239.1464" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="229.22514808626974" cy="222.35893333333334" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="312.07571385176186" cy="231.36626666666666" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="394.92627961725395" cy="207.428" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="478.68729116038884" cy="216.39600000000002" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="562.4483027035237" cy="223.08266666666668" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="645.2988684690158" cy="142.42573333333334" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="727.2389884568652" cy="176.92893333333336" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle><circle cx="811" cy="194.66826666666668" r="4" fill="#a0d0e0" stroke="#ffffff" stroke-width="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></circle>
                                        </svg>
                                        <div class="morris-hover morris-default-style" style="left: 348.426px; top: 111px; display: none;">
                                            <div class="morris-hover-row-label">
                                                2012 Q1
                                            </div>
                                            <div class="morris-hover-point" style="color: #a0d0e0">
                                                Item 1:
                                                6,810
                                            </div>
                                            <div class="morris-hover-point" style="color: #3c8dbc">
                                                Item 2:
                                                1,914
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                                        <svg height="300" version="1.1" width="866" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden; position: relative;">
                                            <desc style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                Created with Raphaël 2.1.0
                                            </desc><defs style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></defs><path fill="none" stroke="#3c8dbc" d="M433,243.33333333333331A93.33333333333333,93.33333333333333,0,0,0,521.227755194977,180.44625304313007" stroke-width="2" opacity="0" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 0;"></path><path fill="#3c8dbc" stroke="#ffffff" d="M433,246.33333333333331A96.33333333333333,96.33333333333333,0,0,0,524.0636473262442,181.4248826052307L560.6151459070204,194.03833029452744A135,135,0,0,1,433,285Z" stroke-width="3" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><path fill="none" stroke="#f56954" d="M521.227755194977,180.44625304313007A93.33333333333333,93.33333333333333,0,0,0,349.28484627831415,108.73398312817662" stroke-width="2" opacity="1" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 1;"></path><path fill="#f56954" stroke="#ffffff" d="M524.0636473262442,181.4248826052307A96.33333333333333,96.33333333333333,0,0,0,346.59400205154566,107.40757544301087L307.42726941747117,88.10097469226493A140,140,0,0,1,565.3416327924656,195.6693795646951Z" stroke-width="3" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path><path fill="none" stroke="#00a65a" d="M349.28484627831415,108.73398312817662A93.33333333333333,93.33333333333333,0,0,0,432.97067846904883,243.333328727518" stroke-width="2" opacity="0" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); opacity: 0;"></path><path fill="#00a65a" stroke="#ffffff" d="M346.59400205154566,107.40757544301087A96.33333333333333,96.33333333333333,0,0,0,432.96973599126824,246.3333285794739L432.9575884998742,284.9999933380171A135,135,0,0,1,311.9120097954186,90.31165416754118Z" stroke-width="3" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);"></path>
                                            <text x="433" y="140" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#000000" font-size="15px" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: 800; font-stretch: normal; font-size: 15px; line-height: normal; font-family: Arial;" font-weight="800" transform="matrix(1,0,0,1,0,0)">
                                                <tspan dy="140" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    In-Store Sales
                                                </tspan>
                                            </text>
                                            <text x="433" y="160" text-anchor="middle" font="10px &quot;Arial&quot;" stroke="none" fill="#000000" font-size="14px" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0); text-anchor: middle; font-style: normal; font-variant: normal; font-weight: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Arial;" transform="matrix(1,0,0,1,0,0)">
                                                <tspan dy="160" style="-webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
                                                    30
                                                </tspan>
                                            </text>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?php endif;?>
                            <!-- /.nav-tabs-custom -->
                        </section>
                    </div>
                </div>

                <!-- /.box-body -->
            </div>
        </div>
    </div>
@endsection
