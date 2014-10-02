// Using float prefix
var floatResizeTimeout;

function floatOnResize() {
	$('body').css('margin-bottom', $('.float-buttons.list-view').outerHeight());
}

$(window).resize(function() {
	clearTimeout(floatResizeTimeout);
	floatResizeTimeout = setTimeout(floatOnResize, 50);
});
