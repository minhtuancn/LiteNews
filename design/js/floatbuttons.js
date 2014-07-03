// Using float prefix

var floatBody, floatElement, floatTimeout, floatOpacityDelay;

$(document).ready(function() {
	floatBody = $('body');
	floatElement = $('#top-buttons');
	$(window).trigger('scroll');
});

function floatOnScroll() {
	if(floatElement.css('opacity') < 1.0) {
		clearTimeout(floatOpacityDelay);
		floatOpacityDelay = setTimeout(
			function() { floatElement.animate({'opacity': 1.0}, 400); },
			1000
		);
	}
	
	if($(window).scrollTop() >= floatElement.outerHeight()) {
		if(floatElement.hasClass('button-group-float')) {
			return;
		}
		
		floatElement.toggle();
		floatElement.addClass('button-group-float');
		floatBody.css('margin-top', floatElement.outerHeight());
		floatBody.css('margin-bottom', floatElement.outerHeight());
		floatElement.fadeToggle();
	}
	else {
		if(!floatElement.hasClass('button-group-float')) {
			return;
		}
		
		floatElement.toggle();
		floatElement.removeClass('button-group-float');
		floatBody.removeAttr('style');
		floatElement.fadeToggle();
	}
}

$(window).scroll(function() {
	if(	floatElement.css('opacity') > 0.2
		&& $(window).scrollTop() >= floatElement.outerHeight()
		&& $(window).scrollTop() + $(window).height() < $(document).height() - 50) {
		floatElement.css('opacity', 0.2);
	}
	
	clearTimeout(floatTimeout);
	floatTimeout = setTimeout(floatOnScroll, 200);
});
