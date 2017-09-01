jQuery(function(){
	var eHead = parent.document.getElementsByTagName('head')[0];
	var eScript = document.createElement('script');
	eScript.src = 'https://adc.tamedia.com.tw/rmadp/static/js/mraid.js';
	eHead.appendChild(eScript);
	eScript = document.createElement('script');
	eScript.src = 'https://adc.tamedia.com.tw/rmadp/static/js/messenger.js';
	eHead.appendChild(eScript);
	eScript = document.createElement('script');
	eScript.src = 'https://adc.tamedia.com.tw/rmadp/static/js/gm-sdk3-mobile.js';
	eHead.appendChild(eScript);

	showCoverAd();
});

function showCoverAd(){
	var w=320, h=480;

	var eAdover = document.createElement('div');
	eAdover.id = 'adover';
	document.body.appendChild(eAdover);

	var eAdoverInner = document.createElement('div');
	eAdoverInner.id = 'adover-inner';
	eAdover.appendChild(eAdoverInner);

	var eAdBanner = document.createElement('div');
	eAdBanner.className = 'ad-banner';
	eAdBanner.innerHTML='<iframe src="http://events.ctitv.com.tw/glidetest/ad/tamedia" width="'+w+'" height="'+h+'" frameborder="0"></iframe>';

	eAdoverInner.appendChild(eAdBanner);
	var eAdWord = document.createElement('div');
	eAdWord.className = 'ad-word';
	eAdoverInner.appendChild(eAdWord);
	var eAdCloseBtn = document.createElement('div');
	eAdCloseBtn.id = 'ad-close-btn';
	eAdCloseBtn.innerHTML='X';
	eAdoverInner.appendChild(eAdCloseBtn);
	
	eAdover.style.display='block';
	if (navigator.userAgent.match(/Android|HTC|iPhone|iPad/i)) {
		
		//document.body.style.position='fixed';
		// document.body.style.overflow='hidden';
	}
}