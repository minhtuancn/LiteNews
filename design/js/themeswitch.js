$(document).ready(function() {
	$('.themeSwitch a').click(function(event) {
		event.preventDefault();
		var id = this.id.substr(5, 1);
		$('#themeSwitchForm').attr("action", window.location.pathname);
		$('#themeSwitchForm input').attr("value", id);
		$('#themeSwitchForm').submit();
	});
});