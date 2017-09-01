var mFrom;

jQuery(function(){
	var hostnameArray = location.hostname.split('.');
	var fName=hostnameArray[0];
	console.log('host name = '+fName);
	
	mFrom=fName+'web';
	// if( document.cookie.indexOf(mFrom) < 0 ) {
		//set cookie
		var d = new Date();
		// d.setTime(d.getTime() + (60 * 60 * 1000));// 一小時後過期
		d.setTime(d.getTime() + (1 * 60 * 1000));// 1分鐘後過期
		var expires = "expires=" + d.toGMTString();
		document.cookie = "name="+mFrom + "; " + expires + '; path=/';
		
		jQuery.ajax({
			type: "GET",
			url: "http://bepo.ctitv.com.tw/bepoapp/services/ad?from="+mFrom+"&callback=?", //使用JSONP務必在結尾使用 GET 的 callback=?
			dataType: "jsonp", 
			success: function (res){
						if(res.result){
							showCoverAd(res);
						}						
					}
		});
	// }
});

function showCoverAd(obj){
	// var obj = jQuery.parseJSON(jsonAd);
	// console.log('obj.data = '+obj.data);
	// if(obj.data.length == undefined) {
		console.log('type = '+obj.data.type);
		var w, h;
		switch(obj.data.type){
			case '1':
				w=320; h=230; break;
				// w=360; h=640; break;
			default:
				w=320; h=480; break;
		}

		var eAdover = document.createElement('div');
		eAdover.id = 'adover';
		document.body.appendChild(eAdover);

		var eAdoverInner = document.createElement('div');
		eAdoverInner.id = 'adover-inner';
		eAdover.appendChild(eAdoverInner);

		var eAdBanner = document.createElement('div');
		eAdBanner.className = 'ad-banner';

		//if (navigator.userAgent.match(/safari/i))
			//eAdBanner.innerHTML='<iframe src="http://events.ctitv.com.tw/glidetest/img.html" width="'+w+'" height="'+h+'" frameborder="0"></iframe>';
		//else
			eAdBanner.innerHTML='<iframe src="'+obj.data.ad_url+'" width="'+w+'" height="'+h+'" frameborder="0"></iframe>';


		eAdoverInner.appendChild(eAdBanner);
		var eAdWord = document.createElement('div');
		eAdWord.className = 'ad-word';
		eAdoverInner.appendChild(eAdWord);
		var eAdCloseBtn = document.createElement('div');
		eAdCloseBtn.id = 'ad-close-btn';
		eAdCloseBtn.innerHTML='×';
		eAdoverInner.appendChild(eAdCloseBtn);

		if (navigator.userAgent.match(/Android|HTC|iPhone|iPad/i)) {
			eAdover.style.display='block';
			//document.body.style.position='fixed';
			// document.body.style.overflow='hidden';
		}
		(function adOver(){
			jQuery(eAdCloseBtn).click(function() {
				eAdover.style.display='none';
				eAdBanner.innerHTML='';
				//document.body.style.position=null;
				// document.body.style.overflow='auto';
			});
		})();
	// }
}