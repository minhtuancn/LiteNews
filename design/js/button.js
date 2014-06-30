// Using button prefix

var buttonTimeout;

function buttonMaxHeight(selector) {
	var maxHeight = 0;
	$(selector).each(function() {
		if($(this).outerHeight() > maxHeight) {
			maxHeight = $(this).outerHeight();
		}
	});
	return maxHeight;
}

$(document).ready(function() {
	$('.button').each(function() {
		var width = $(this).width();
		var p = $(this).find('p');
		if(width - 40 < p.width())
			p.width(width - 40);
	});
	
	buttonOnResize();
});

function buttonOnResize() {
	var height = buttonMaxHeight('.button');
	$('.button').each(function() {
		if($(this).outerHeight() < height) {
			var padding = height - $(this).outerHeight() - 6;
			$(this).css("padding-top", padding);
			$(this).css("padding-bottom", padding);
		}
	});
}

$(window).resize(function() {
	clearTimeout(buttonTimeout);
	buttonTimeout = setTimeout(buttonOnResize, 200);
});