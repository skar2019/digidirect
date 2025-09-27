define(['jquery'], function ($) {
  'use strict';

  $(function () {
    /* ========================
       ✅ Sticky Header (smooth)
    ======================== */
    const $header = $('.header.content');
    let stickyPoint = 0;
    let isSticky = false;

    function recalcStickyPoint() {
      if (!isSticky && $header.length) {
        stickyPoint = $header.offset().top;
      }
    }

    function setSticky(active) {
      const $aaPanel = $('.aa-Panel');

      if (active && !isSticky) {
        $header.addClass('is-sticky');
        isSticky = true;

        if ($aaPanel.length) {
          const headerHeight = $header.outerHeight();
          $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px');
        }
      } else if (!active && isSticky) {
        $header.removeClass('is-sticky');
        isSticky = false;

        if ($aaPanel.length) {
          $aaPanel.removeClass('is-sticky').css('top', '');
        }
      }
    }

    function updateSticky() {
      const scrollTop = $(window).scrollTop();
      setSticky(scrollTop >= stickyPoint);
    }

    // ✅ Initial setup
    setTimeout(() => {
      recalcStickyPoint();
      updateSticky();
    }, 300);

    $(window).on('scroll', updateSticky);
    $(window).on('resize', function () {
      recalcStickyPoint();
      updateSticky();
    });

    if (window.MutationObserver) {
      const observer = new MutationObserver(() => {
        setTimeout(() => {
          recalcStickyPoint();
          updateSticky();
        }, 200);
      });
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style'],
      });
    }

    /* ========================
       🌀 Owl Carousel 2-Finger Swipe
    ======================== */
    const $carousels = $('.owl-carousel.custom');

    $carousels.each(function () {
      const $carousel = $(this);

      $carousel.owlCarousel({
        items: 1,
        loop: true,
        autoplay: false,
        dots: true,
        nav: false,
        mouseDrag: false, // disable single-finger drag
        touchDrag: false  // disable default swipe
      });

      let startX = 0;
      let activeCarousel = null;

      // 📱 2-finger swipe detection
      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          activeCarousel = $carousel; // only handle current
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          e.preventDefault(); // prevent browser gestures
        }
      });

      $carousel.on('touchmove', function (e) {
        if (!activeCarousel || activeCarousel[0] !== $carousel[0]) return;

        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          const currentX = (touches[0].clientX + touches[1].clientX) / 2;
          const deltaX = currentX - startX;

          const threshold = 80; // adjust for sensitivity
          if (Math.abs(deltaX) > threshold) {
            if (deltaX > 0) {
              $carousel.trigger('prev.owl.carousel');
            } else {
              $carousel.trigger('next.owl.carousel');
            }
            startX = currentX; // reset for smooth next move
          }
          e.preventDefault(); // avoid browser navigation
        }
      });

      $carousel.on('touchend', function () {
        activeCarousel = null;
      });

      // 💻 Trackpad 2-finger horizontal swipe
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault(); // prevent page scroll or back nav
          if (event.deltaX > 0) {
            $carousel.trigger('next.owl.carousel');
          } else {
            $carousel.trigger('prev.owl.carousel');
          }
        }
      });
    });
  });
});
