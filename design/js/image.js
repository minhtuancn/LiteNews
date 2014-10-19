$(document).ready(function() {
    $('body').on('click', '.imageButton', function(e) {
        e.preventDefault();
        image = $(this).parent().find('.articleImage');
        
        if(!image.hasClass('visible')) {
            image.addClass('visible').attr('src', image.attr('data-url'));
            /* TODO: Clean this mess up */
            $(this).css({'height': '0px', 'display': 'none'});
            $(this).blur();
            imageSwitchLabel($(this));
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
        imageSwitchLabel($(this).parent().find('.imageButton'));
    });
});

function imageSwitchLabel(el) {
    var content = el.find('p');
    var original = content.text();
    content.text(el.attr('data-alt'));
    el.attr('data-alt', original);
    el.parent().find('.date').css('margin-top', el.height());
}

function imageSwitchCookie(value) {
    $.ajax({
        type: "POST",
        url: ajaxGetUrl() + "/ajaxload",
        data: {
            showImages: value
        }
    });
}
