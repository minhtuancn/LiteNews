// Using float prefix

var floatActive = true,
	floatBody,
	floatElement,
	floatScrollTimeout,
	floatResizeTimeout,
	floatOpacityDelay;

$(document).ready(function() {
	floatBody = $('body');
	floatElement = $('.float-buttons.list-view');
	
	if(floatElement.length == 0) {
		floatActive = false;
		return;
	}
	
	$(window).trigger('scroll');
	$(window).trigger('resize');
});

function floatOnScroll() {
	floatElement.stop().animate({'opacity': 1.0}, 1000);
}

$(window).scroll(function() {
	if(!floatActive) {
		return;
	}
	
	if(floatElement.css('opacity') > 0.2 && $(window).scrollTop() + $(window).height() < $(document).height() - 50) {
		floatElement.css('opacity', 0.2);
	}
	
	clearTimeout(floatScrollTimeout);
	floatScrollTimeout = setTimeout(floatOnScroll, 1000);
});

function floatOnResize() {
	floatBody.css('margin-bottom', floatElement.outerHeight());
}

$(window).resize(function() {
	if(!floatActive) {
		return;
	}
	
	clearTimeout(floatResizeTimeout);
	floatResizeTimeout = setTimeout(floatOnResize, 50);
});
