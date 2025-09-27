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
        
        $('.owl-carousel').each(function () {
            const $carousel = $(this);

            //Prevent accidental navigation during swipe
            let isDragging = false;
            let startX = 0;

            $carousel.on('mousedown touchstart', function (e) {
              isDragging = false;
              startX = e.pageX || e.originalEvent.touches[0].pageX;
            });

            $carousel.on('mousemove touchmove', function (e) {
              const x = e.pageX || e.originalEvent.touches[0].pageX;
              if (Math.abs(x - startX) > 10) {
                isDragging = true;
              }
            });

            $carousel.find('a').on('click', function (e) {
              if (isDragging) {
                e.preventDefault(); // ⛔ Stop link navigation if it was a swipe
              }
            });
        });

    });
});

