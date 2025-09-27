define(['jquery'], function ($) {
  'use strict';

    $(function () {
        const $header = $('.header.content');
        if (!$header.length) return;

        const stickyPoint = $header.offset().top;

        // Function to initialize once .aa-Panel exists
        function initSticky() {
            const $aaPanel = $('.aa-Panel');
            if (!$aaPanel.length) {
                setTimeout(initSticky, 200); // retry every 200ms until found
                return;
            }

            $(window).on('scroll', function () {
                const scrollTop = $(window).scrollTop();

                if (scrollTop >= stickyPoint) {
                    $header.addClass('is-sticky');

                    // Calculate header height dynamically
                    const headerHeight = $header.outerHeight();
                    $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px');
                } else {
                    $header.removeClass('is-sticky');
                    $aaPanel.removeClass('is-sticky').css('top', '');
                }
            });
        }

        initSticky();
    });
});

