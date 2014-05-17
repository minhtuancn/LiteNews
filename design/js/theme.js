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
});

$(window).resize(function() {
	var height = maxHeight('.button');
	$('.button').each(function() {
		if($(this).outerHeight() < height) {
			var padding = height - $(this).outerHeight() - 6;
			$(this).css("padding-top", padding);
			$(this).css("padding-bottom", padding);
		}
	});
});
