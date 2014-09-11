// Using ajax prefix

var ajaxTimeout, ajaxScrollPos;

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
		var url = ajaxGetUrl();
		url = url + "/ajaxload/article" + articleUrl.substr(url.length);
		
		$.get(
			url,
			function(data) {
				$('.ajaxArticle')
					.html(data)
					.scrollTop(0);
				
				ajaxOnResize();
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

function ajaxOnResize() {
	var padding = 58;
	if($(window).width() >= 1224) {
		padding += 10;
	}
	
	$('.ajaxArticle').css('padding-bottom', padding);
}

$(window).resize(function() {
	clearTimeout(ajaxTimeout);
	ajaxTimeout = setTimeout(ajaxOnResize, 100);
});
