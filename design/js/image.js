$(document).ready(function() {
    $('body').on('click', '.imageButton', function(e) {
        e.preventDefault();
        image = $('.articleImage');
        
        if(!image.hasClass('visible')) {
            image.addClass('visible').attr('src', image.attr('data-url'));
            $(this).blur();
        }
        else if(!$(this).hasClass('active')) {
            $(this).addClass('active');
            imageSwitchCookie(1);
        }
        else {
            $(this).removeClass('active');
            $(this).blur();
            imageSwitchCookie(0)
        }
    });
    
    $('body').on('click', '.articleImage', function(e) {
        $(this).removeClass('visible');
        $('.imageButton').removeClass('active');
    });
});

function imageSwitchCookie(value) {
    /* Clear preloaded cache because we need to refresh HTML */
   ajaxPreLoadData = [];
    
    $.ajax({
        type: "POST",
        url: ajaxGetUrl() + "/ajaxload",
        data: {
            showImages: value
        }
    });
}
