// Using sprite prefix

var spriteTimeout;

function spriteOnScroll() {
	$('.indexLink .spriteContainer').each(function() {
		var img = $(this).find('img');
		var width = img.width();
		var height;
		
		if(width <= 200) {
			height = 30;
		}
		else if(width >= 400) {
			height = 60;
		}
		else {
			height = width * 0.15;
		}
		
		$(this).height(height+"px");
		img.css('top', "-" + (img.attr('data-logo') * height) + "px");
	});
}

$(document).ready(function() {
	spriteOnScroll();
});

$(window).resize(function() {
	clearTimeout(spriteTimeout);
	spriteTimeout = setTimeout(spriteOnScroll, 100);
});
