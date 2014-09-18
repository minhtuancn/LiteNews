$(document).ready(function() {
	var links = $('.indexLink');
	if(links.length == 0) {
		return;
	}
	
	$.get(
		"cache/logo.svg",
		function(data) {
			var container = links.parent();
			var arr = data.split("\n");
			var id, row, link;
			
			for(i = 0; i < arr.length; i++) {
				id = parseInt(arr[i].substr(0, 2));
				rowData = arr[i].substr(2, arr[i].length - 2);
				
				if(rowData.length > 0) {
					link = container.find(".indexLink[data-id='"+id+"']");
					link.html('<div class="logo" style="max-width: '+link.attr('data-width')+'%;">'+rowData+'</div>');
				}
			}
		},
		'html'
	);
});
