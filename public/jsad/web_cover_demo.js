// var mFrom = 'gotvweb';

var Loader = function() {}
Loader.prototype = {
    require: function(scripts, callback) {
        this.loadCount = 0;
        this.totalRequired = scripts.length;
        this.callback = callback;

        for (var i = 0; i < scripts.length; i++) {
            this.writeScript(scripts[i]);
        }
    },
    loaded: function(evt) {
        this.loadCount++;

        if (this.loadCount == this.totalRequired && typeof this.callback == 'function') this.callback.call();
    },
    writeScript: function(src) {
        var self = this;
        var s = document.createElement('script');
        s.type = "text/javascript";
        s.async = true;
        s.src = src;
        s.addEventListener('load', function(e) { self.loaded(e); }, false);
        var head = document.getElementsByTagName('head')[0];
        head.appendChild(s);
    }
}

var l = new Loader();
l.require([
        'https://adc.tamedia.com.tw/rmadp/static/js/mraid.js',
        'https://adc.tamedia.com.tw/rmadp/static/js/messenger.js',
        'https://adc.tamedia.com.tw/rmadp/static/js/gm-sdk3-mobile.js'
    ],
    function() {
        console.log('All Scripts Loaded');
        taMediaAdLoad();
    });

function taMediaAdLoad() {
    //body 加入 div#MADdpzone
    jQuery('body').prepend('<div id="MADdpzone"></div>');
    //從網址取得廣告版位ID
    var adPID = 'k36149387727277315g'; //gotv廣告版位ID
    var hostChar1 = location.hostname.split('.')[0]; //網址的第一個單字(gotv.ctitv.com.tw就取得gotv)
    if (hostChar1 == '119') { //********正式站 改成bepo
        adPID = 'FMw1493876902208vik';
        // mFrom = 'demoweb'; //***********正式站 改成bepoweb
    }
    console.log('hostname : ' + location.hostname);
    ctitvCoverAd();
    //TAMedia ad load
    // adLoad2(adPID, '198', 'MADdpzone', true, 1, adLoad_callback);
}

//adLoad2() callback
function adLoad_callback(status) {
    console.log("callback status : " + status);
    if (status == '20')
        ctitvCoverAd();
}

function ctitvCoverAd() {
    // $.ajax({
    //     url: 'http://119.81.243.76/services/ad/?from=' + mFrom,
    //     dataType: "jsonp",
    //     success: function(dataArray) {
    //         if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    //             if (dataArray['data']['type'] == 3) {
    //                 var adContent = dataArray['data']['embed'];
    //                 $('body').prepend(adContent);
    //                 $.ajax({
    //                     url: dataArray['data']['ad_url'],
    //                     dataType: "jsonp text"
    //                 });
    //             } else {
    //                 var script = document.createElement('script');
    //                 script.type = 'text/javascript';
    //                 script.src = 'http://bepo.ctitv.com.tw/bepoapp/jsad/web_cover.js';
    //                 $('head').append('<link href="http://bepo.ctitv.com.tw/bepoapp/css/adblock.css" rel="stylesheet">');
    //                 $('head').append(script);
    //             }
    //         }
    //     }
    // });
}

function ctitvAdGo() {
    jQuery('head').append(gAdHead);
    // jQuery('body').prepend(gAdBody);
}

var gAdHead = '<script async="async" src="https://www.googletagservices.com/tag/js/gpt.js"></script>\
<script>\
  var googletag = googletag || {};\
  googletag.cmd = googletag.cmd || [];\
</script>\
\
<script>\
  googletag.cmd.push(function() {\
    googletag.defineSlot("/32672392/Ctitv_CoverAD_Passback", [[320, 280], [300, 250], [320, 480]], "div-gpt-ad-1494907717987-0").addService(googletag.pubads());\
    googletag.pubads().enableSingleRequest();\
    googletag.pubads().collapseEmptyDivs();\
    googletag.enableServices();\
  });\
</script>';

var gAdBody = '<!-- /32672392/Ctitv_CoverAD_Passback -->\
<div id="div-gpt-ad-1494907717987-0">\
<script>\
googletag.cmd.push(function() { googletag.display("div-gpt-ad-1494907717987-0"); });\
</script>\
</div>';
