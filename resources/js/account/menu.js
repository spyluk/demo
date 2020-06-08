/**
 * Created by sergei on 26.10.18.
 */
$( document ).ready(function() {
    $('[data-toggle="dropdown"]').click(function(e){
        $('.dropdown-menu.show').removeClass('show');
        var menu = $(this).next();
        if(menu.hasClass('show'))
        {
            menu.removeClass('show');
        } else {
            menu.addClass('show');
        }

        var menu_padding = 10;
        var menu_pos = menu.offset().left + menu.width() + menu_padding;
        var window_width = $( window ).width();

        if(menu_pos > window_width) {
            var pos = menu_pos - window_width;
            $(this).next().css({'margin-left': '-'+pos+'px'});
        }
        e.stopPropagation();

        return false;
    });

    $('.dropdown-menu.show').click(function(e) {
        e.stopPropagation();
    });

    $(document.body).click( function() {
        $('.dropdown-menu.show').removeClass('show');
    });

        // Left menu collapse
        $('.button-menu-mobile').on('click', function (event) {
            event.preventDefault();
            $("body").toggleClass("enlarged");
        });

    if ($(window).width() < 1025) {
                $('body').addClass('enlarged');
            } else {
                $('body').removeClass('enlarged');
            }
});