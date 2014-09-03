var responsiveTimeout;

function responsiveOnResize() {
	var counter, width = $(window).width();
	
	if(width >= 768) {
		$('.indexDescription').each(function() {
			counter = 0;
			$(this)
				.nextUntil($(this), '.indexLink')
				.each(function() {
					++counter;
					if(width >= 1224) {
						$(this).css('margin-left', (counter % 3 == 2 || counter % 3 == 0 ? '2%' : '0px'));
					}
					else {
						$(this).css('margin-left', (counter % 2 == 0 ? '2%' : '0px'));
					}
				});
		});
	}
	else {
		$('.indexLink').each(function() {
			$(this).css('margin-left', '0px');
		});
	}
}

$(document).ready(function() {
	responsiveOnResize();
});

$(window).resize(function() {
	clearTimeout(responsiveTimeout);
	responsiveTimeout = setTimeout(responsiveOnResize, 100);
});
