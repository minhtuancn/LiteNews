$(document).ready(function() {
	$('.loadTitles').click(function(event) {
		event.preventDefault();
		
		$.get(
			$(this).attr('data-load') + "/" + $('.titleLink').length,
			function(data) {
				$('.titleLink').last().after(data);
			}
		);
	});
});