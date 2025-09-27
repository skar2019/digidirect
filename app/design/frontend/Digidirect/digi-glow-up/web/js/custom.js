define(['jquery'], function ($) {
  'use strict';

  $(function () {
    /* ========================
       ✅ Sticky Header
    ======================== */
    const $header = $('.header.content');
    let stickyPoint = 0;

    function recalcStickyPoint() {
      stickyPoint = $header.length ? $header.offset().top : 0;
    }

    function updateSticky() {
      const scrollTop = $(window).scrollTop();
      const $aaPanel = $('.aa-Panel');

      if (scrollTop >= stickyPoint) {
        $header.addClass('is-sticky');
        if ($aaPanel.length) {
          const headerHeight = $header.outerHeight();
          $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px');
        }
      } else {
        $header.removeClass('is-sticky');
        if ($aaPanel.length) {
          $aaPanel.removeClass('is-sticky').css('top', '');
        }
      }
    }

    // Listen for scroll and resize
    $(window).on('scroll', updateSticky);
    $(window).on('resize', function () {
      recalcStickyPoint();
      updateSticky();
    });

    // Mutation observer for aa-Panel
    if (window.MutationObserver) {
      const observer = new MutationObserver(() => {
        setTimeout(() => {
          recalcStickyPoint();
          updateSticky();
        }, 50);
      });
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style'],
      });
    }

    // Run once on load
    setTimeout(() => {
      recalcStickyPoint();
      updateSticky();
    }, 300);

    /* ========================
       🌀 Owl Carousel
    ======================== */
    $('.owl-carousel').each(function () {
      const $carousel = $(this);

      // Avoid re-initializing
      if ($carousel.hasClass('owl-loaded')) return;

      // Initialize after short delay to ensure correct width
      setTimeout(() => {
        $carousel.owlCarousel({
          items: 1,
          loop: true,
          dots: true,
          nav: false,
          mouseDrag: false,
          touchDrag: false,
          smartSpeed: 600
        });

        // --- 2-finger swipe ---
        let startX = 0;
        let lastSwipeTime = 0;
        const SWIPE_THRESHOLD = 100;
        const COOLDOWN = 500;

        $carousel.on('touchstart', function (e) {
          const touches = e.originalEvent.touches;
          if (touches && touches.length === 2) {
            startX = (touches[0].clientX + touches[1].clientX) / 2;
          }
        });

        $carousel.on('touchmove', function (e) {
          const touches = e.originalEvent.touches;
          if (!touches || touches.length !== 2) return;
          const currentX = (touches[0].clientX + touches[1].clientX) / 2;
          const deltaX = currentX - startX;
          const now = Date.now();

          if (Math.abs(deltaX) > SWIPE_THRESHOLD && now - lastSwipeTime > COOLDOWN) {
            e.preventDefault();
            if (deltaX < 0) {
              $carousel.trigger('next.owl.carousel', [600]);
            } else {
              $carousel.trigger('prev.owl.carousel', [600]);
            }
            lastSwipeTime = now;
            startX = currentX;
          }
        });

        let wheelAccum = 0;
        $carousel.on('wheel', function (e) {
          const event = e.originalEvent;
          if (!event) return;
          if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
            e.preventDefault();
            wheelAccum += event.deltaX;
            const now = Date.now();
            if (Math.abs(wheelAccum) > SWIPE_THRESHOLD && now - lastSwipeTime > COOLDOWN) {
              if (wheelAccum > 0) {
                $carousel.trigger('next.owl.carousel', [600]);
              } else {
                $carousel.trigger('prev.owl.carousel', [600]);
              }
              lastSwipeTime = now;
              wheelAccum = 0;
            }
          }
        }, { passive: false });
      }, 400); // wait 400ms after layout
    });
  });
});
