// Using float prefix

var floatBody, floatElement, floatTimeout;

$(document).ready(function() {
	floatBody = $('body');
	floatElement = $('#top-buttons');
	$(window).trigger('scroll');
});

function floatOnScroll() {
	if(floatElement.css('opacity') < 1.0) {
		setTimeout(
			function() { floatElement.animate({'opacity': 1.0}, 400); },
			800
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
	if($(window).scrollTop() >= floatElement.outerHeight() && floatElement.css('opacity') == 1.0 && $(window).scrollTop() + $(window).height() < $(document).height() - 50) {
		floatElement.animate({'opacity': 0.2}, 100);
	}
	
	clearTimeout(floatTimeout);
	floatTimeout = setTimeout(floatOnScroll, 200);
});
