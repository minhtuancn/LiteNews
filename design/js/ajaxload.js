// Using ajax prefix

var ajaxTimeout;

$(document).ready(function() {
	$('.loadTitles').click(function(e) {
		e.preventDefault();
		
		var origValue = $(this).html();
		$(this).html('<i class="fa fa-refresh fa-spin"></i>');
		
		$.get(
			$(this).attr('data-load') + "/" + $('.titleLink').length,
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
		
		if($(window).width() >= 1224) {
			e.preventDefault();
			
			var top = $('.titleLink').position().top;
			
			$('.ajaxArticle')
				.html('<div class="contentBox"><i class="fa fa-refresh fa-spin"></i></div>')
				.css('top', top)
				.css('display', 'inline-block');
				
			$('.ajaxArticle > .contentBox').css('height', $(window).height() - top - 58);
			
			var articleUrl = $(this).attr('href');
			var url = ajaxGetUrl();
			url = url + "/ajaxload/article" + articleUrl.substr(url.length);
			
			$.get(
				url,
				function(data) {
					$('.ajaxArticle')
						.html(data)
						.css('height', 'auto')
						.scrollTop(0);
					
					ajaxOnResize();
				}
			);
		}
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
	var top = $('.titleLink').position().top;
	
	$('.ajaxArticle')
		.css('top', top)
		.css('max-height', $(window).height() - top - 58);
}

$(window).resize(function() {
	clearTimeout(ajaxTimeout);
	ajaxTimeout = setTimeout(ajaxOnResize, 200);
});
