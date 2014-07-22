// Using float prefix

var floatBody, floatElement, floatTimeout, floatOpacityDelay;

$(document).ready(function() {
	floatBody = $('body');
	floatElement = $('#top-buttons');
	$(window).trigger('scroll');
	$(window).trigger('resize');
});

function floatOnScroll() {
	if(floatElement.css('opacity') < 1.0) {
		clearTimeout(floatOpacityDelay);
		floatOpacityDelay = setTimeout(
			function() { floatElement.animate({'opacity': 1.0}, 400); },
			1000
		);
	}
}

$(window).scroll(function() {
	if(floatElement.css('opacity') > 0.2 && $(window).scrollTop() + $(window).height() < $(document).height() - 50) {
		floatElement.css('opacity', 0.2);
	}
	
	clearTimeout(floatTimeout);
	floatTimeout = setTimeout(floatOnScroll, 200);
});

$(window).resize(function() {
	floatBody.css('margin-bottom', floatElement.outerHeight());
});
