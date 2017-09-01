<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="{{asset('/css/news2013m.css')}}" rel="stylesheet" type="text/css" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
	<!--
        <script src="{{ asset('/js/jquery.min.js') }}"></script>
	-->
        <title>{{$data->title}}</title>
        <!-- GA start -->
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
ga(function() {
var newTracker = ga.getByName('newTracker');
});

            ga('create', 'UA-12207318-18', 'auto');
            ga('create', 'UA-12207318-23', 'auto', 'WebViewTracker');               
            ga('send', 'pageview');
            ga('WebViewTracker.send', 'pageview');
          
</script>
<!-- GA end -->
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
    <body>
        <div id='wrap'>
<!-- Bepo_App_ContentTop -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3614921727460755"
     data-ad-slot="2820178624"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
            <div id='container'>
                <h2>{{$data->title}}</h2>
                <div class='story'>
                    <p class='date'>
                        {{DateHelper::zhDateTime($data->created_at)}}
                    </p>
                    <p id='news-content'>
                        <?php
                        function nl2p($str) {
                            $arr=explode("\n",$str);
                            $out='';
                            for($i=0;$i < count($arr);$i++) {
                                if(strlen(trim($arr[$i])) > 0)
                                    $out.='<p>'.trim($arr[$i]).'</p>';
                            }
                            return $out;
                        }
						function add_videoWrapper($html){
							$html = preg_replace("/<iframe\s(.+?)<\/iframe>/is", "<div class='videoWrapper'><iframe $1</iframe></div>", $html);
							return $html;
						}
                        function removeRelation($data){
                            $keyword='※延伸亂亂讀';
                            $arr=explode($keyword,$data,2);
                            if(count($arr)==2){
                                return $arr[0];
                            }
                            $data=trim($data);
							
							//part2
							$keyword='※延伸閱讀';
                            $arr=explode($keyword,$data,2);
                            if(count($arr)==2){
                                return $arr[0];
                            }
                            $data=trim($data);
                            return $data;
                        }
                        ?>
                        {!! nl2p(removeRelation(add_videoWrapper($data->content)))!!}
                    </p>
                </div>
                <br />
                <br />
                <br />
            </div>
		<!-- Bepo_App_ContentBottom_RWD -->
		<ins class="adsbygoogle"
			 style="display:block"
			 data-ad-client="ca-pub-3614921727460755"
			 data-ad-slot="6353897822"
			 data-ad-format="auto"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>        
		<script>
		$('img,a').on('click',function(event){
			event.stopPropagation();
			return false;
		});
		</script>
		</div>
    </body>
</html>
