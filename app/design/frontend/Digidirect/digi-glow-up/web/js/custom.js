define(['jquery'], function ($) {
  'use strict';

  $(function () {
    /* ========================
       ✅ Sticky Header (unchanged)
    ======================== */
    const $header = $('.header.content');
    let stickyPoint = 0;
    let isSticky = false;

    function recalcStickyPoint() {
      if (!isSticky && $header.length) {
        stickyPoint = $header.offset().top;
      }
    }

    function positionAAPanel() {
      const $aaPanel = $('.aa-Panel');
      if ($aaPanel.length && isSticky) {
        const headerHeight = $header.outerHeight();
        $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px');
      }
    }

    function removeAAPanelSticky() {
      const $aaPanel = $('.aa-Panel');
      if ($aaPanel.length) {
        $aaPanel.removeClass('is-sticky').css('top', '');
      }
    }

    function setSticky(active) {
      if (active && !isSticky) {
        $header.addClass('is-sticky');
        isSticky = true;
        positionAAPanel(); // 🧠 immediately position aa-Panel
      } else if (!active && isSticky) {
        $header.removeClass('is-sticky');
        isSticky = false;
        removeAAPanelSticky();
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

    // 🧠 Watch DOM for aa-Panel activation
    if (window.MutationObserver) {
      const observer = new MutationObserver(() => {
        setTimeout(() => {
          recalcStickyPoint();
          updateSticky();
          positionAAPanel(); // 👈 reposition immediately if sticky
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
       🌀 Owl Carousel 2-Finger Swipe (1 item per gesture)
    ======================== */
    const $carousels = $('.owl-carousel');
    const threshold = 150; // 🎚 adjust sensitivity (px)

    $carousels.each(function () {
      const $carousel = $(this);
      let startX = 0;
      let active = false;
      let hasSwiped = false;

      function getOwl() {
        return $carousel.data('owl.carousel');
      }

      // 📱 2-finger swipe detection
      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          active = true;
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          hasSwiped = false;
          e.preventDefault(); // prevent browser gestures
        }
      });

      $carousel.on('touchmove', function (e) {
        if (!active || hasSwiped) return;
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          const currentX = (touches[0].clientX + touches[1].clientX) / 2;
          const deltaX = currentX - startX;

          if (Math.abs(deltaX) > threshold) {
            const owl = getOwl();
            if (!owl) return;

            const currentIndex = owl.relative(owl.current());
            const targetIndex = deltaX > 0 ? currentIndex - 1 : currentIndex + 1;

            // ✅ Move exactly 1 item smoothly
            $carousel.trigger('to.owl.carousel', [targetIndex, 500]);
            hasSwiped = true;
          }
          e.preventDefault(); // prevent back navigation
        }
      });

      $carousel.on('touchend', function () {
        active = false;
        hasSwiped = false;
      });

      // 💻 Trackpad horizontal swipe
      $carousel.on('wheel', function (e) {
        const ev = e.originalEvent;
        if (Math.abs(ev.deltaX) > Math.abs(ev.deltaY)) {
          e.preventDefault();
          const owl = getOwl();
          if (!owl) return;

          const currentIndex = owl.relative(owl.current());
          const targetIndex = ev.deltaX > 0 ? currentIndex + 1 : currentIndex - 1;
          $carousel.trigger('to.owl.carousel', [targetIndex, 500]);
        }
      });
    });
  });
});
