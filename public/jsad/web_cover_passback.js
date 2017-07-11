var eAdover = document.createElement('div');
eAdover.id = 'adover';
document.body.appendChild(eAdover);

var eAdoverInner = document.createElement('div');
eAdoverInner.id = 'adover-inner';
eAdover.appendChild(eAdoverInner);

var eAdBanner = document.createElement('div');
eAdBanner.className = 'ad-banner';

eAdBanner.innerHTML = '<div id="div-gpt-ad-1494907717987-0">\
<script>\
googletag.cmd.push(function() { googletag.display("div-gpt-ad-1494907717987-0"); });\
</script>\
</div>';


eAdoverInner.appendChild(eAdBanner);
var eAdWord = document.createElement('div');
eAdWord.className = 'ad-word';
eAdoverInner.appendChild(eAdWord);
var eAdCloseBtn = document.createElement('div');
eAdCloseBtn.id = 'ad-close-btn';
eAdCloseBtn.innerHTML = 'X';
eAdoverInner.appendChild(eAdCloseBtn);

// if (document.getElementById('google_ads_iframe_/32672392/Ctitv_CoverAD_Passback_0') != null) {
//     if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
//         eAdover.style.display = 'block';
//     }
// }

// if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    eAdover.style.display = 'block';
// }
(function adOver() {
    jQuery(eAdCloseBtn).click(function() {
        eAdover.style.display = 'none';
        eAdBanner.innerHTML = '';
    });
})();
