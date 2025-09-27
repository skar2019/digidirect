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

        let startX = 0;

        // 📱 Mobile: detect 2-finger swipe
        $carousel.on('touchstart', function (e) {
          if (e.originalEvent.touches.length === 2) {
            const touches = e.originalEvent.touches;
            startX = (touches[0].clientX + touches[1].clientX) / 2;
          }
        });

        $carousel.on('touchmove', function (e) {
          if (e.originalEvent.touches.length === 2) {
            const touches = e.originalEvent.touches;
            const currentX = (touches[0].clientX + touches[1].clientX) / 2;
            const deltaX = currentX - startX;

            if (Math.abs(deltaX) > 50) { // swipe threshold
              if (deltaX > 0) {
                $carousel.trigger('prev.owl.carousel');
              } else {
                $carousel.trigger('next.owl.carousel');
              }
              startX = currentX; // reset for next move
            }
          }
        });

        // 💻 Desktop: detect 2-finger trackpad swipe (wheel)
        $carousel.on('wheel', function (e) {
          const event = e.originalEvent;
          // horizontal scroll dominates
          if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
            e.preventDefault(); // stop page scroll
            if (event.deltaX > 0) {
              $carousel.trigger('next.owl.carousel');
            } else {
              $carousel.trigger('prev.owl.carousel');
            }
          }
        });

    });
});

