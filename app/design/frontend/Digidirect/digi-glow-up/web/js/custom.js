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
       🌀 Owl Carousel 2-Finger Swipe (1 slide per swipe)
    ======================== */
    const $carousels = $('.owl-carousel');

    $carousels.each(function () {
      const $carousel = $(this);
      const owlData = $carousel.data('owl.carousel'); // will be set after initialization

      let startX = 0;
      let activeCarousel = null;
      let hasSwiped = false;
      const threshold = 120; // 🎚️ adjust sensitivity

      // 📱 2-finger swipe detection
      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          activeCarousel = $carousel;
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          hasSwiped = false;
          e.preventDefault();
        }
      });

      $carousel.on('touchmove', function (e) {
        if (!activeCarousel || activeCarousel[0] !== $carousel[0]) return;
        const touches = e.originalEvent.touches;
        if (touches.length === 2 && !hasSwiped) {
          const currentX = (touches[0].clientX + touches[1].clientX) / 2;
          const deltaX = currentX - startX;

          if (Math.abs(deltaX) > threshold) {
            const owl = $carousel.data('owl.carousel');
            const currentIndex = owl.relative(owl.current());

            if (deltaX > 0) {
              // 👈 Swipe Right → previous item
              $carousel.trigger('to.owl.carousel', [currentIndex - 1, 600, true]);
            } else {
              // 👉 Swipe Left → next item
              $carousel.trigger('to.owl.carousel', [currentIndex + 1, 600, true]);
            }

            hasSwiped = true;
          }
          e.preventDefault();
        }
      });

      $carousel.on('touchend', function () {
        activeCarousel = null;
        hasSwiped = false;
      });

      // 💻 Trackpad 2-finger horizontal swipe
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault();
          const owl = $carousel.data('owl.carousel');
          const currentIndex = owl.relative(owl.current());

          if (event.deltaX > 0) {
            $carousel.trigger('to.owl.carousel', [currentIndex + 1, 600, true]);
          } else {
            $carousel.trigger('to.owl.carousel', [currentIndex - 1, 600, true]);
          }
        }
      });
    });
  });
});
