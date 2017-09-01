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
        'https://cdn.innity.net/admanager.js'
    ],
    function() {
        console.log('innity js loaded');
        executeAsync(innityAdLoad);
        // innityAdLoad();
    });

function innityAdLoad() {
    innity_pcu = "%%CLICK_URL_UNESC%%";
    new innity_adZone("ea96efc03b9a050d895110db8c4af057", "61310", {});
    console.log('new innity_adZone() done');
}

function executeAsync(func) {
    setTimeout(func, 0);
}

