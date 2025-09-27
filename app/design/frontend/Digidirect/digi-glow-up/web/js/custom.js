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
        positionAAPanel();
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
          positionAAPanel();
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
       🌀 Owl Carousel: Enable mouse drag, convert 2-finger scroll into drag
    ======================== */
    const $carousels = $('.owl-carousel');

    $carousels.each(function () {
      const $carousel = $(this);

      // 🖱️ Simulate drag on 2-finger swipe (trackpad)
      let isScrolling = false;
      let scrollTimeout = null;

      $carousel.on('wheel', function (e) {
        const ev = e.originalEvent;

        // Only handle horizontal gestures (2-finger swipe)
        if (Math.abs(ev.deltaX) > Math.abs(ev.deltaY)) {
          e.preventDefault();

          if (isScrolling) return; // prevent spamming
          isScrolling = true;

          if (ev.deltaX > 0) {
            $carousel.trigger('next.owl.carousel', [300]);
          } else {
            $carousel.trigger('prev.owl.carousel', [300]);
          }

          // Reset after delay
          clearTimeout(scrollTimeout);
          scrollTimeout = setTimeout(() => {
            isScrolling = false;
          }, 400);
        }
      });
    });
  });
});
