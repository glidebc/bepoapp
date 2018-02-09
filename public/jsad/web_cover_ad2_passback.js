// var LoaderAd2 = function() {}
// LoaderAd2.prototype = {
//     require: function(scripts, callback) {
//         this.loadCount = 0;
//         this.totalRequired = scripts.length;
//         this.callback = callback;

//         for (var i = 0; i < scripts.length; i++) {
//             this.writeScript(scripts[i]);
//         }
//     },
//     loaded: function(evt) {
//         this.loadCount++;

//         if (this.loadCount == this.totalRequired && typeof this.callback == 'function') this.callback.call();
//     },
//     writeScript: function(src) {
//         var self = this;
//         var s = document.createElement('script');
//         s.id = "MediaScroll-AD2";
//         s.key = "1c583b65-56f8-11e7-91c1-f23c9173ed43";
//         s.type = "text/javascript";
//         s.show = "now";
//         s.close = "";
//         s.async = true;
//         s.src = src;
//         s.addEventListener('load', function(e) { self.loaded(e); }, false);
//         var head = document.getElementsByTagName('head')[0];
//         head.appendChild(s);
//     }
// }
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

// var loaderAd2 = new LoaderAd2();
// loaderAd2.require([
//         'https://content.ad2iction.com/mediascroll/ad2-scroll.js'
//     ],
//     function() {
//         console.log('AD2 loaded');
//     });

function callbackAD2() {
    console.log("AD2 無廣告");
    console.log("Passback 準備中");
    passbackAdLoad();
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
