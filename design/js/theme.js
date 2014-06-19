function formAction(formID, value) {
	$(formID).attr("action", window.location.pathname);
	$(formID + " input").attr("value", value);
	$(formID).submit();
}

function maxHeight(selector) {
	var maxHeight = 0;
	$(selector).each(function() {
		if($(this).outerHeight() > maxHeight) {
			maxHeight = $(this).outerHeight();
		}
	});
	return maxHeight;
}

$(document).ready(function() {
	$('.langSwitch a').click(function(event) {
		event.preventDefault();
		formAction("#langSwitchForm", this.id.substr(5, 2));
	});
	
	$('.themeSwitch a').click(function(event) {
		event.preventDefault();
		formAction("#themeSwitchForm", this.id.substr(6, 1));
	});
	
	$('.button').each(function() {
		var width = $(this).width();
		var p = $(this).find('p');
		if(width - 40 < p.width())
			p.width(width - 40);
	});
	
	$(window).resize();
	$(window).scroll();
});

function onResize() {
	var height = maxHeight('.button');
	$('.button').each(function() {
		if($(this).outerHeight() < height) {
			var padding = height - $(this).outerHeight() - 6;
			$(this).css("padding-top", padding);
			$(this).css("padding-bottom", padding);
		}
	});
}

var resizeTimeout;
$(window).resize(function() {
	resizeTimeOut = setTimeout(onResize, 200);
});

function onScroll() {
	var body = $('body');
	var topButtons = $('#top-buttons');
	
	if(topButtons.css('opacity') < 1.0) {
		setTimeout(
			function() { topButtons.animate({'opacity': 1.0}, 200); },
			500
		);
	}
	
	if($(window).scrollTop() >= topButtons.outerHeight()) {
		if(topButtons.hasClass('button-group-float')) {
			return;
		}
		
		topButtons.toggle();
		topButtons.addClass('button-group-float');
		body.css('margin-top', topButtons.outerHeight());
		body.css('margin-bottom', topButtons.outerHeight());
		topButtons.fadeToggle();
	}
	else {
		if(!topButtons.hasClass('button-group-float')) {
			return;
		}
		
		topButtons.toggle();
		topButtons.removeClass('button-group-float');
		body.removeAttr('style');
		topButtons.fadeToggle();
	}
}

var scrollTimeout;
$(window).scroll(function() {
	var topButtons = $('#top-buttons');
	if($(window).scrollTop() >= topButtons.outerHeight() && topButtons.css('opacity') == 1.0) {
		topButtons.animate({'opacity': 0.2}, 100);
	}
	
	clearTimeout(scrollTimeout);
	scrollTimeout = setTimeout(onScroll, 200);
});
