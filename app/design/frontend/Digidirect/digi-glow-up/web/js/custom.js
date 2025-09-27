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
        
        let lastScrollTime = 0;
        $carousel.on('wheel', function (e) {
          const deltaX = e.originalEvent.deltaX;
          const deltaY = e.originalEvent.deltaY;
          const now = Date.now();

          // Only handle mostly-horizontal scrolls
          if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 5) {
            e.preventDefault();

            // Add slight debounce to prevent overscrolling
            if (now - lastScrollTime > 400) {
              if (deltaX > 0) $(this).trigger('next.owl.carousel');
              else $(this).trigger('prev.owl.carousel');
              lastScrollTime = now;
            }
          }
        });

    });
});

