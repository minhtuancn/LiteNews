// Using ajax prefix

var ajaxTimeout,
	ajaxScrollPos,
	ajaxPreLoadTimeout,
	ajaxPreLoadData = [],
	ajaxPreLoadQueue = [];

$(document).ready(function() {	
	$('.loadTitles').click(function(e) {
		e.preventDefault();
		
		var origValue = $(this).html();
		$(this).html('<i class="fa fa-refresh fa-spin"></i>');
		
		var offsetDate = $('.titleLink').last().find('.titleInfo').text();
		offsetDate = offsetDate.substr(offsetDate.length - 14, 14).split('.');
		var offsetTime = offsetDate[2].split(':');
		offsetTime[0] = offsetTime[0].substr(3, 2);
		offsetDate[1] = (parseInt(offsetDate[1]) - 1).toString();
		offsetDate[2] = (parseInt(offsetDate[2].substr(0, 2)) + 2000).toString();
		var offsetTimestamp = new Date(offsetDate[2], offsetDate[1], offsetDate[0], offsetTime[0], offsetTime[1]);
		offsetTimestamp = offsetTimestamp.getTime() / 1000;
		
		$.get(
			$(this).attr('data-load') + "/" + offsetTimestamp,
			function(data) {
				$('.titleLink').last().after(data);
				$('.loadTitles').html(origValue);
			}
		);
	});
	
	$('body').delegate('.titleLink', 'click', function(e) {
		if($(this).hasClass('weatherLink')) {
			return;
		}
		
		e.preventDefault();
		
		$('.ajaxArticle')
			.html('<div class="contentBox"><i class="fa fa-refresh fa-spin"></i></div>')
			.css('display', 'inline-block');
		
		ajaxOnResize();
		
		ajaxScrollPos = $(document).scrollTop();
		if($(window).width() < 1224) {
			$('body').css('overflow', 'hidden');
		}
		
		var articleUrl = $(this).attr('href');
		if(articleUrl in ajaxPreLoadData) {
			ajaxArticleComplete(ajaxPreLoadData[articleUrl]);
			return;
		}
		
		var url = ajaxGetUrl();
		url = url + "/ajaxload/article" + articleUrl.substr(url.length);
		
		$.get(
			url,
			function(data) {
				ajaxArticleComplete(data);
			}
		);
	});
	
	$('body').delegate('.close-ajax', 'click', function(e) {
		e.preventDefault();
		
		$('body').css('overflow', 'visible');
		$('.ajaxArticle').css('display', 'none');
		$(document).scrollTop(ajaxScrollPos);
	});
	
	if($('.weather').length > 0) {
		navigator.geolocation.getCurrentPosition(function (location) {
			var url = ajaxGetUrl() + "/ajaxload/weather/" + location.coords.latitude + "/" + location.coords.longitude;
			$.get(
				url,
				function(data) {
					$('.weather').html(data);
				}
			);
		});
	}
	
	$('.weatherLink').click(function(e) {
		e.preventDefault();
		
		$('.weather').html('<div class="contentBox"><i class="fa fa-refresh fa-spin"></i></div>');
		$('html,body').scrollTop(0);
		
		var url = ajaxGetUrl() + "/ajaxload/weather/" + $(this).text();
		$.get(
			url,
			function(data) {
				$('.weather').html(data);
			}
		);
	});
	
	$(window).scroll();
	$(window).resize();
});

function ajaxGetUrl() {
	var url = document.URL;
	var strIndex = url.lastIndexOf("/");
	if(url.length - 1 == strIndex) {
		url = url.substr(0, strIndex);
		url = url.substr(0, url.lastIndexOf("/"));
	}
	else {
		url = url.substr(0, strIndex);
	}
	return url;
}

function ajaxArticleComplete(data) {
	$('.ajaxArticle')
		.html(data)
		.scrollTop(0);
	
	ajaxOnResize();
}

function ajaxPreLoad(keepQueue) {
	if(!keepQueue || ajaxPreLoadQueue.length == 0) {
		ajaxPreLoadQueue = [];
		
		var titles = $('.titleLink');
		if(titles.hasClass('weatherLink')) {
			return;
		}
		
		var scrollTop = $(window).scrollTop();
		var scrollBottom = scrollTop + $(window).height(); 
		var titleTop;
		titles.each(function() {
			titleTop = $(this).position().top;
			if(scrollTop - 100 < titleTop && scrollBottom + 100 > titleTop && !($(this).attr('href') in ajaxPreLoadData)) {
				ajaxPreLoadQueue.push($(this).attr('href'));
			}
		});
	}
	
	if(ajaxPreLoadQueue.length == 0) {
		return;
	}
	
	var articleUrl = ajaxPreLoadQueue.shift();
	var url = ajaxGetUrl();
	url = url + "/ajaxload/article" + articleUrl.substr(url.length);
	$.get(
		url,
		function(data) {
			ajaxPreLoadData[articleUrl] = data;
		}
	);
	
	clearTimeout(ajaxPreLoadTimeout);
	if(ajaxPreLoadQueue.length > 0) {
		ajaxPreLoadTimeout = setTimeout(function() { ajaxPreLoad(true); }, 100);
	}
}

$(window).scroll(function() {
	clearTimeout(ajaxPreLoadTimeout);
	ajaxPreLoadTimeout = setTimeout(function() { ajaxPreLoad(false); }, 200);
});

function ajaxOnResize() {
	var padding = 58;
	if($(window).width() >= 1224) {
		padding += 10;
		$('body').css('overflow', 'visible');
	}
	else if($('.ajaxArticle .contentBox').length > 0 && $('.ajaxArticle').is(':visible')) {
		$('body').css('overflow', 'hidden');
	}
	
	$('.ajaxArticle').css('padding-bottom', padding);
}

$(window).resize(function() {
	clearTimeout(ajaxTimeout);
	ajaxTimeout = setTimeout(ajaxOnResize, 100);
});
