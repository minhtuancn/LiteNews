// Using button prefix

var buttonTimeout,
	buttonSelector = '.button';

function buttonMaxHeight(elements) {
	var maxHeight = 0;
	elements.each(function() {
		$(this).removeAttr('style');
		if($(this).outerHeight() > maxHeight) {
			maxHeight = $(this).outerHeight();
		}
	});
	return maxHeight;
}

function buttonOnResize() {
	var parents = [];
	
	$(buttonSelector).each(function() {
		var parent = $(this).parent();
		if($.inArray(parent, parents) == -1) {
			parents.push(parent);
		}
	});
	
	$.each(parents, function(index, parent) {
		var buttons = parent.find(">"+buttonSelector);
		var height = buttonMaxHeight(buttons);
		buttons.each(function() {
			if($(this).outerHeight() < height) {
				var padding = (height - $(this).outerHeight()) / 2 + 5;
				$(this).css("padding-top", padding);
				$(this).css("padding-bottom", padding);
			}
		});
	});
}

$(document).ready(function() {
	$('.button').each(function() {
		var width = $(this).width();
		var p = $(this).find('p');
		if(width - 40 < p.width())
			p.width(width - 40);
	});
	
	$('.button.refresh').click(function(e) {
		e.preventDefault();
		document.location.reload(true);
		$('html,body').scrollTop(0);
	});
	
	buttonOnResize();
});

$(window).resize(function() {
	clearTimeout(buttonTimeout);
	buttonTimeout = setTimeout(buttonOnResize, 200);
});