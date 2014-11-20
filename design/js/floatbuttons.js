// Using float prefix
var floatResizeTimeout;

function floatOnResize() {
	$('body').css('margin-bottom', $('body>.floatButtons').outerHeight());
}

$(window).resize(function() {
	clearTimeout(floatResizeTimeout);
	floatResizeTimeout = setTimeout(floatOnResize, 50);
});

/* fucking webkit */
$(document).ready(function () {
        $('.ajaxArticle').scroll(function () {
            var contentBox = $(this).find('.contentBox');
            if(contentBox.css('opacity') != 1.0) {
                contentBox.css('opacity', 1.0);
            }
            else {
                contentBox.css('opacity', 0.99);
            }
        });
});
