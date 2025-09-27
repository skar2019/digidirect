define(['jquery'], function ($) {
  'use strict';

  $(function () {
    $('.owl-carousel').each(function () {
      const $carousel = $(this);

      $carousel.owlCarousel({
        items: 1,
        loop: true,
        dots: true,
        nav: false,
        mouseDrag: false,
        touchDrag: false,
        smartSpeed: 600
      });

      let startX = 0;
      let lastSwipeTime = 0;
      const SWIPE_THRESHOLD = 100;
      const COOLDOWN = 500;

      // 📱 2-finger touch
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
          e.preventDefault(); // ✅ stop native gesture
          if (deltaX < 0) {
            $carousel.trigger('next.owl.carousel', [600]);
          } else {
            $carousel.trigger('prev.owl.carousel', [600]);
          }
          lastSwipeTime = now;
          startX = currentX;
        }
      });

      // 💻 Trackpad
      let wheelAccum = 0;
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (!event) return;

        // ✅ Always stop browser history swipe if horizontal
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault(); // prevent browser back/forward
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
      }, { passive: false }); // ⚠️ passive:false lets preventDefault work
    });
  });
});
