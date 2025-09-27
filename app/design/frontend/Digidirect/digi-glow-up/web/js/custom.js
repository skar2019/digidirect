define(['jquery'], function ($) {
  'use strict';

  $(function () {
    // ---------- Sticky header + aa-Panel handling ----------
    const $header = $('.header.content');
    if ($header.length) {
      let stickyPoint = $header.offset().top;

      function recalcStickyPoint() {
        // recalc (useful on resize or if header layout changes)
        stickyPoint = $header.offset().top;
      }

      // flag to avoid responding to our own DOM updates
      let updatingAaPanel = false;

      function updateSticky() {
        const scrollTop = $(window).scrollTop();
        const $aaPanel = $('.aa-Panel');

        // mark we're updating so MutationObserver ignores this change
        updatingAaPanel = true;

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

        // small delay then clear guard (lets the mutation observer ignore our modifications)
        setTimeout(function () { updatingAaPanel = false; }, 50);
      }

      // run on scroll and on load
      $(window).on('scroll', updateSticky);
      $(window).on('resize', function () { recalcStickyPoint(); updateSticky(); });

      // initial calculate + apply
      recalcStickyPoint();
      updateSticky();

      // ---------- MutationObserver to detect .aa-Panel appearing / changing ----------
      // throttle timer for multiple rapid mutations
      let observerTimer = null;

      if (window.MutationObserver) {
        const observer = new MutationObserver(function (mutations) {
          if (updatingAaPanel) return; // ignore changes we made ourselves

          let found = false;
          for (let m of mutations) {
            if (m.type === 'childList') {
              // check added nodes for .aa-Panel or container that contains it
              for (let n of m.addedNodes) {
                if (n.nodeType !== 1) continue;
                if (n.matches && n.matches('.aa-Panel')) { found = true; break; }
                if ($(n).find('.aa-Panel').length) { found = true; break; }
              }
              if (found) break;
            }
            if (m.type === 'attributes') {
              // attribute changes on .aa-Panel or parents could indicate activation/visibility change
              if ($(m.target).is('.aa-Panel') || $(m.target).find('.aa-Panel').length) { found = true; break; }
            }
          }

          if (found) {
            clearTimeout(observerTimer);
            observerTimer = setTimeout(function () {
              // recalc in case layout shifted and apply sticky immediately
              recalcStickyPoint();
              updateSticky();
            }, 40); // tiny debounce
          }
        });

        observer.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class', 'style'] });

        // optional: expose observer to debug console
        window._aaPanelObserver = observer;
      } else {
        // fallback simple poll if MutationObserver unsupported
        let pollCount = 0;
        const poll = setInterval(function () {
          pollCount++;
          if ($('.aa-Panel').length || pollCount > 50) {
            recalcStickyPoint();
            updateSticky();
            clearInterval(poll);
          }
        }, 200);
      }
    }

    // ---------- Per-carousel (instance-specific) 2-finger swipe logic ----------
    $('.owl-carousel').each(function () {
      const $carousel = $(this);

      // Initialize carousel if not already
      if (typeof $carousel.owlCarousel === 'function' && !$carousel.data('owl.carousel')) {
        $carousel.owlCarousel({
          items: 1,
          loop: true,
          autoplay: false,
          dots: true,
          nav: false,
          mouseDrag: false,
          touchDrag: false,
          smartSpeed: 600
        });
      }

      let startX = 0;
      let lastSwipeTime = 0;
      let wheelAccum = 0;
      const SWIPE_THRESHOLD = 120;
      const COOLDOWN = 700;

      // touchstart for 2-finger detection
      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent && e.originalEvent.touches;
        if (touches && touches.length >= 2) {
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          wheelAccum = 0;
        }
      });

      // touchmove (2-finger)
      $carousel.on('touchmove', function (e) {
        const touches = e.originalEvent && e.originalEvent.touches;
        if (!(touches && touches.length >= 2)) return;

        const currentX = (touches[0].clientX + touches[1].clientX) / 2;
        const deltaX = currentX - startX;
        const now = Date.now();

        if (Math.abs(deltaX) > SWIPE_THRESHOLD && (now - lastSwipeTime) > COOLDOWN) {
          if (deltaX > 0) {
            $carousel.trigger('prev.owl.carousel', [600]);
          } else {
            $carousel.trigger('next.owl.carousel', [600]);
          }
          lastSwipeTime = now;
          startX = currentX;
          wheelAccum = 0;
        }
      });

      // reset on touchend/cancel
      $carousel.on('touchend touchcancel', function () {
        wheelAccum = 0;
      });

      // wheel (trackpad) only affects this carousel instance
      $carousel.on('wheel', function (e) {
        const event = e.originalEvent;
        if (!event) return;
        // only horizontal-dominant gestures
        if (Math.abs(event.deltaX) <= Math.abs(event.deltaY)) return;

        wheelAccum += event.deltaX;
        const now = Date.now();

        if (Math.abs(wheelAccum) > SWIPE_THRESHOLD && (now - lastSwipeTime) > COOLDOWN) {
          e.preventDefault();
          if (wheelAccum > 0) {
            $carousel.trigger('next.owl.carousel', [600]);
          } else {
            $carousel.trigger('prev.owl.carousel', [600]);
          }
          lastSwipeTime = now;
          wheelAccum = 0;
        }
      });
    });
  });
});
