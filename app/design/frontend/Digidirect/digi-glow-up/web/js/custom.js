define(['jquery'], function ($) {
  'use strict';

  $(function () {
    const $header = $('.header.content');
    if (!$header.length) return;

    const stickyPoint = $header.offset().top;

    function updateSticky() {
      const scrollTop = $(window).scrollTop();
      const $aaPanel = $('.aa-Panel');
      if (!$aaPanel.length) return;

      if (scrollTop >= stickyPoint) {
        $header.addClass('is-sticky');
        const headerHeight = $header.outerHeight();
        $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px');
      } else {
        $header.removeClass('is-sticky');
        $aaPanel.removeClass('is-sticky').css('top', '');
      }
    }

    // 🔹 Wait for .aa-Panel, then attach scroll + initial check
    function initSticky() {
      const $aaPanel = $('.aa-Panel');
      if (!$aaPanel.length) {
        setTimeout(initSticky, 200);
        return;
      }

      $(window).on('scroll', updateSticky);
      updateSticky(); // 👈 run immediately on first load
    }
    initSticky();

    // 🔹 Carousel 2-finger swipe logic
    const $carousel = $('.owl-carousel'); // adjust selector if needed

    let startX = 0;
    let lastSwipeTime = 0;
    const SWIPE_THRESHOLD = 120;
    const COOLDOWN = 500;

    // 📱 Touch: 2-finger swipe
    $carousel.on('touchstart', function (e) {
      const touches = e.originalEvent.touches;
      if (touches.length === 2) {
        startX = (touches[0].clientX + touches[1].clientX) / 2;
      }
    });

    $carousel.on('touchmove', function (e) {
      const touches = e.originalEvent.touches;
      if (touches.length === 2) {
        const currentX = (touches[0].clientX + touches[1].clientX) / 2;
        const deltaX = currentX - startX;
        const now = Date.now();

        if (Math.abs(deltaX) > SWIPE_THRESHOLD && now - lastSwipeTime > COOLDOWN) {
          if (deltaX > 0) {
            $carousel.trigger('prev.owl.carousel');
          } else {
            $carousel.trigger('next.owl.carousel');
          }
          lastSwipeTime = now;
          startX = currentX;
        }
      }
    });

    // 💻 Trackpad: horizontal 2-finger swipe
    let wheelAccum = 0;
    $carousel.on('wheel', function (e) {
      const event = e.originalEvent;
      if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
        wheelAccum += event.deltaX;
        const now = Date.now();

        if (Math.abs(wheelAccum) > SWIPE_THRESHOLD && now - lastSwipeTime > COOLDOWN) {
          e.preventDefault();
          if (wheelAccum > 0) {
            $carousel.trigger('next.owl.carousel');
          } else {
            $carousel.trigger('prev.owl.carousel');
          }
          wheelAccum = 0;
          lastSwipeTime = now;
        }
      }
    });
  });
});
