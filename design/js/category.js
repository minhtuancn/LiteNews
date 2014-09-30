// Using category prefix

$(document).ready(function() {
	$('.categorySelect').fastClick(function(e) {
		e.preventDefault();
		$('.categoryContainer').slideToggle();
	});
});
