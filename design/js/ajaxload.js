// Using ajax prefix

var ajaxTimeout;

$(document).ready(function() {
	$('.loadTitles').click(function(e) {
		e.preventDefault();
		
		$.get(
			$(this).attr('data-load') + "/" + $('.titleLink').length,
			function(data) {
				$('.titleLink').last().after(data);
			}
		);
	});
	
	$('body').delegate('.titleLink', 'click', function(e) {
		if($(window).width() >= 1224) {
			e.preventDefault();
			
			var top = $('.titleLink').position().top;
			
			$('.ajaxArticle')
				.html('<div class="contentBox"><i class="fa fa-refresh fa-spin"></i></div>')
				.css('top', top)
				.css('display', 'inline-block');
				
			$('.ajaxArticle > .contentBox').css('height', $(window).height() - top - 58);
			
			var url = document.URL;
			var strIndex = url.lastIndexOf("/");
			if(url.length - 1 == strIndex) {
				url = url.substr(0, strIndex);
				url = url.substr(0, url.lastIndexOf("/"));
			}
			else {
				url = url.substr(0, strIndex);
			}
			
			var articleUrl = $(this).attr('href');
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
});

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
