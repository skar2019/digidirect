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
       🌀 Owl Carousel 2-Finger Swipe (Smooth, 1 Item)
    ======================== */
    const $carousels = $('.owl-carousel');

    $carousels.each(function () {
      const $carousel = $(this);
      let startX = 0;
      let activeCarousel = null;
      let hasSwiped = false;
      const threshold = 120; // 🎚 sensitivity (px distance)
      const lockDuration = 250; // ⏱ lock time to avoid multiple triggers

      // 📱 2-finger swipe detection
      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          activeCarousel = $carousel;
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          hasSwiped = false;
          e.preventDefault(); // stop browser gestures
        }
      });

      $carousel.on('touchmove', function (e) {
        if (!activeCarousel || activeCarousel[0] !== $carousel[0]) return;

        const touches = e.originalEvent.touches;
        if (touches.length === 2 && !hasSwiped) {
          const currentX = (touches[0].clientX + touches[1].clientX) / 2;
          const deltaX = currentX - startX;

          if (Math.abs(deltaX) > threshold) {
            if (deltaX > 0) {
              $carousel.trigger('prev.owl.carousel', [100]); // smooth 300ms
            } else {
              $carousel.trigger('next.owl.carousel', [100]);
            }
            hasSwiped = true;

            // unlock for next gesture after short delay
            setTimeout(() => {
              hasSwiped = false;
              activeCarousel = null;
            }, lockDuration);
          }

          e.preventDefault(); // block browser back/forward gesture
        }
      });

      $carousel.on('touchend', function () {
        activeCarousel = null;
      });

      // 💻 Trackpad horizontal scroll (2-finger swipe)
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault();
          if (hasSwiped) return;
          hasSwiped = true;

          if (event.deltaX > 0) {
            $carousel.trigger('next.owl.carousel', [100]);
          } else {
            $carousel.trigger('prev.owl.carousel', [100]);
          }

          setTimeout(() => {
            hasSwiped = false;
          }, lockDuration);
        }
      });
    });
  });
});
