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
        console.log('ta media js loaded');
        taMediaAdLoad();
    });

function taMediaAdLoad() {
    //body 加入 div#MADdpzone
    jQuery('body').prepend('<div id="MADdpzone"></div>');
    //從網址取得廣告版位ID
    console.log('hostname : ' + location.hostname);
    var adPID = 'FMw1493876902208vik'; //bepo廣告版位ID
    var hostChar1 = location.hostname.split('.')[0]; //網址的第一個單字(gotv.ctitv.com.tw就取得gotv)
    if (hostChar1 == 'gotv') { //網址第一個字是gotv
        adPID = 'k36149387727277315g'; //gotv廣告版位ID
    } else {
        hostChar1 = "bepo";
    }
    console.log(hostChar1 + ' 廣告版位ID = ' + adPID);
    // passbackAdLoad();
    //TAMedia ad load
    adLoad2(adPID, '198', 'MADdpzone', true, 0, adLoad_callback); //

    /*
    adLoad2()
    參數定義:
    第一個,廣告版位ID 
    第二個,廠商ID (198)
    第三個,自定義的網頁元素id (MADdpzone)
    第四個,openNewWindow,布林值(true|false)
    第五個,testFlag, 數值(0|1), 0表示為正式模式, 1表示為測試模式
    第六個,callbackFunction,為callback介面, 必須為function type, 有廣告回傳:"00", 無廣告回傳:"20"
    第七個,floatPosition, 數值(1|2), 為懸浮廣告使用, 若為1表示廣告位置在左邊, 若為2表示廣告位置在右邊
 
    參數說明:
    前兩個參數不需變動(於後台廣告版位列表的下載嵌入程式碼自動取得),
    第三個參數自行定義,以不重複為原則,並和廣告位置<div>的id相同
    第四個參數自行設定,設為true表示點擊廣告後另開新分頁，設為false表示點擊廣告後不另開新分頁
    */
}

//adLoad2() callback
function adLoad_callback(status) {

    if (status == '00') {
        console.log("ta media AD");
    } else {
        console.log("ta media 無廣告");
        console.log("Passback 準備中");
        passbackAdLoad();
    }
}

function passbackAdLoad() {
    jQuery('head').append('<link href="http://bepo.ctitv.com.tw/bepoapp/css/adblock.css" rel="stylesheet">');
    jQuery('body').append(passbackAd); //蓋版廣告的div

    l = new Loader();
    l.require([
            'https://www.googletagservices.com/tag/js/gpt.js',
            'http://bepo.ctitv.com.tw/bepoapp/jsad/web_cover_passback_head.js' //Passback 在Head中的 script
        ],
        function() {
            var s = document.createElement('script');
            var t = document.createTextNode('googletag.cmd.push(function() { googletag.display("div-gpt-ad-1494907717987-0"); });' + 'document.getElementById("adover").style.display = "block";');
            s.appendChild(t);
            document.getElementById('div-gpt-ad-1494907717987-0').appendChild(s);

            console.log('Passback 載入完畢');
        });
}

var passbackAd = '<div id="adover" style="display: none;">\
    <div id="adover-inner">\
        <div class="ad-banner" id="ad-banner">\
            <div id="div-gpt-ad-1494907717987-0">\
            </div>\
        </div>\
        <div class="ad-word"></div>\
        <div id="ad-close-btn" onclick="adOver();">×</div>\
    </div>\
</div>\
<script>\
function adOver(){\
    document.getElementById("adover").style.display = "none";\
    document.getElementById("ad-banner").innerHTML = "";\
}\
</script>';
