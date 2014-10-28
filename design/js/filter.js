$(document).ready(function() {
    $('.showFilters').fastClick(function(e) {
        e.preventDefault();
        var filterContainer = $('.filterContainer');
        
        if(filterContainer.hasClass('visible')) {
            filterContainer.css('transition-duration', '0s');
            filterContainer.css('max-height', filterContainer.height());
            filterContainer.removeClass('visible');
            setTimeout(function() {
                filterContainer.css('transition-duration', '');
                filterContainer.css('max-height', '');
            }, 10);
        }
        else {
            filterContainer.addClass('visible');
        }
    });
    
    $('.websiteSelect').fastClick(function(e) {
        e.preventDefault();
        $('.categoryContainer').removeClass('visible');
        $('.websiteFilter').toggleClass('visible');
    });
    
    $('.websiteFilter .button').click(function(e) {
        if($(this).attr('id') == "websiteFilterAll" || $(this).attr('id') == "websiteFilterSave") {
            if($(this).attr('id') == "websiteFilterAll") {
                $(this).parent().find('div .button').each(function() {
                    $(this).removeClass('active');
                });
            }
            
            return;
        }
        
        e.preventDefault();
        
        var container = $(this).parent().parent();
        container.find('.button').first().removeClass('active');
        
        $(this).toggleClass('active');
        $(this).blur();
        
        var saveValue = "";
        container.find('div > .button').each(function() {
            if($(this).hasClass('active')) {
                saveValue += $(this).val()+",";
            }
        });
        $('#websiteFilterSave').val(saveValue);
    });
    
	$('.categorySelect').fastClick(function(e) {
		e.preventDefault();
        $('.websiteFilter').removeClass('visible');
		$('.categoryContainer').toggleClass('visible');
	});
	
	$('.categoryContainer .button').click(function() {
	    $(this).parent().parent().find('.button').each(function() {
	        $(this).removeClass('active');
	    });
	});
});
