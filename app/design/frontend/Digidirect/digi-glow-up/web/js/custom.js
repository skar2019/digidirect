define(['jquery'], function ($) {
  'use strict';

  $(function () {
    const $header = $('.header.content');
    if (!$header.length) return;

    const stickyPoint = $header.offset().top;

    // 🔹 Sticky header logic
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

    $(window).on('scroll', updateSticky);
    updateSticky(); // 👈 run immediately

    // 🔹 Carousel with 2-finger smooth swipe
    const $carousel = $('.owl-carousel');
    if (!$carousel.length) return;

    let startX = 0;
    let lastSwipeTime = 0;
    const SWIPE_THRESHOLD = 120; // 👈 how far you swipe before it triggers
    const COOLDOWN = 700;        // 👈 delay between swipes in ms

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
            $carousel.trigger('prev.owl.carousel', [600]); // 👈 smooth duration
          } else {
            $carousel.trigger('next.owl.carousel', [600]);
          }
          lastSwipeTime = now;
          startX = currentX;
        }
      }
    });

    // 💻 Trackpad: 2-finger horizontal scroll
    let wheelAccum = 0;
    $carousel.on('wheel', function (e) {
      const event = e.originalEvent;
      if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
        wheelAccum += event.deltaX;
        const now = Date.now();

        if (Math.abs(wheelAccum) > SWIPE_THRESHOLD && now - lastSwipeTime > COOLDOWN) {
          e.preventDefault();
          if (wheelAccum > 0) {
            $carousel.trigger('next.owl.carousel', [600]); // 👈 smooth
          } else {
            $carousel.trigger('prev.owl.carousel', [600]);
          }
          wheelAccum = 0;
          lastSwipeTime = now;
        }
      }
    });
  });
});
