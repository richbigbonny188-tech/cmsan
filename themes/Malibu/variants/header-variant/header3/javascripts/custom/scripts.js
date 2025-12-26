(function($) {
    $(window).on('JSENGINE_INIT_FINISHED', () => {
        $(window).on('scroll', function() {
            let header = $('#header'),
                scroll = $(window).scrollTop();

            if (scroll >= 1) {
                header.addClass('sticky');
            } else {
                header.removeClass('sticky');
            }
        });
    });
})(jQuery);

