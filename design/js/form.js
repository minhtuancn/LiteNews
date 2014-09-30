// Using form prefix

function formAction(formID, value) {
	$(formID).attr("action", window.location.pathname);
	$(formID + " input").attr("value", value);
	$(formID).submit();
}

$(document).ready(function() {
	$('.langSwitch a').fastClick(function(event) {
		event.preventDefault();
		formAction("#langSwitchForm", this.id.substr(5, 2));
	});
	
	$('.themeSwitch a').fastClick(function(event) {
		event.preventDefault();
		formAction("#themeSwitchForm", this.id.substr(6, 1));
	});
});