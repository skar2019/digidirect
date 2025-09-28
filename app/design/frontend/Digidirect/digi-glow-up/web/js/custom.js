define(['jquery'], function ($) {
  'use strict';

  $(function () {
    /* ========================
       ✅ Sticky Header (with placeholder)
    ======================== */
    const $header = $('.header.content');
    const $placeholder = $('<div class="header-placeholder"></div>');
    $placeholder.hide();
    $header.after($placeholder);

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
        $placeholder.height($header.outerHeight()).show(); // maintain layout
        $header.addClass('is-sticky');
        isSticky = true;
        positionAAPanel();
      } else if (!active && isSticky) {
        $header.removeClass('is-sticky');
        isSticky = false;
        $placeholder.hide(); // restore original position
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
       🌀 Owl Carousel 2-Finger Swipe
    ======================== */
    const $carousels = $('.owl-carousel');

    $carousels.each(function () {
      const $carousel = $(this);
      let startX = 0;
      let activeCarousel = null;
      let hasSwiped = false;
      const threshold = 120;
      const lockDuration = 250;
      const transitionSpeed = 400;

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
            if (deltaX > 0) {
              $carousel.trigger('prev.owl.carousel', [transitionSpeed]);
            } else {
              $carousel.trigger('next.owl.carousel', [transitionSpeed]);
            }
            hasSwiped = true;

            setTimeout(() => {
              hasSwiped = false;
              activeCarousel = null;
            }, lockDuration);
          }
          e.preventDefault();
        }
      });

      $carousel.on('touchend', function () {
        activeCarousel = null;
      });

      // 💻 Trackpad horizontal scroll
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault();
          if (hasSwiped) return;
          hasSwiped = true;

          if (event.deltaX > 0) {
            $carousel.trigger('next.owl.carousel', [transitionSpeed]);
          } else {
            $carousel.trigger('prev.owl.carousel', [transitionSpeed]);
          }

          setTimeout(() => {
            hasSwiped = false;
          }, lockDuration);
        }
      });
    });

    /* ========================
       🔍 Update Autocomplete Header
    ======================== */
    $(document).on('keyup', '#autocomplete-0-input', function () {
      const query = $(this).val().trim();
      setTimeout(function () {
        const $header = $('.aa-Source[data-autocomplete-source-id="products"] .aa-SourceHeader p');
        if ($header.length) {
          $header.text(query.length ? `Results for "${query}"` : 'Top Selling Products');
        }
      }, 100);
    });

    /* ========================
       🛒 Add Class For Minicart Modal
    ======================== */
    const observer2 = new MutationObserver(function () {
      const $minicartModal = $('aside.modal-popup .modal-content #minicart-content-wrapper').closest('aside.modal-popup');
      if ($minicartModal.length && !$minicartModal.hasClass('minicart-modal')) {
        $minicartModal.addClass('minicart-modal');
      }
    });
    observer2.observe(document.body, { childList: true, subtree: true });
  });
});
