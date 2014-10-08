// Using category prefix

$(document).ready(function() {
	$('.categorySelect').fastClick(function(e) {
		e.preventDefault();
		$('.categoryContainer').toggleClass('visible');
	});
	
	$('.categoryContainer .button').fastClick(function() {
	    $(this).parent().find('.button').each(function() {
	        $(this).removeClass('active');
	    });
	});
});
