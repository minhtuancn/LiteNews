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
	
	$('.titleLink').click(function(e) {
		if($(window).width() >= 1224) {
			e.preventDefault();
			
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
					var position = $('.titleLink').position();
					$('.ajaxArticle')
						.html(data)
						.css('display', 'inline-block')
						.css('top', position.top)
						.css('max-height', $(window).height() - position.top - 58);
				}
			);
		}
	});
});