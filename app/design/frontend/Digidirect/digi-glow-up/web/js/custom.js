define(['jquery', 'ko', 'uiRegistry', 'Magento_Ui/js/core/app'], function ($, ko, registry, uiApp) {
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
        $placeholder.height($header.outerHeight()).show();
        $header.addClass('is-sticky');
        isSticky = true;
        positionAAPanel();
      } else if (!active && isSticky) {
        $header.removeClass('is-sticky');
        isSticky = false;
        $placeholder.hide();
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
       🧊 Global Blur Overlay
    ======================== */
    const $blurOverlay = $('<div class="global-blur-overlay"></div>');
    if (!$('.global-blur-overlay').length) {
      $('#maincontent').before($blurOverlay);
    }

    function positionBlurOverlay() {
      const $overlay = $('.global-blur-overlay');
      const $main = $('#maincontent');
      if ($main.length && $overlay.length) {
        const offsetTop = $main.offset().top;
        const documentHeight = Math.max(
          $(document).height(),
          $('body').prop('scrollHeight')
        );

        // Only set height when blur is active
        if ($('body').hasClass('blur-active')) {
          $overlay.css({
            position: 'absolute',
            top: offsetTop + 'px',
            height: (documentHeight - offsetTop) + 'px',
          });
        } else {
          // Initially height 0 to avoid white gap
          $overlay.css({
            position: 'absolute',
            top: offsetTop + 'px',
            height: '0',
          });
        }
      }
    }

    positionBlurOverlay();
    $(window).on('resize scroll', positionBlurOverlay);

    if (window.MutationObserver) {
      const blurObserver = new MutationObserver(() => positionBlurOverlay());
      blurObserver.observe(document.body, { childList: true, subtree: true });
    }

    // CSS for blur
    const blurStyle = `
      .global-blur-overlay {
        width: 100%;
        left: 0;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        opacity: 0;
        transition: opacity 0.3s ease, height 0.3s ease;
        pointer-events: none;
        z-index: 9;
      }
      body.blur-active .global-blur-overlay {
        opacity: 1;
      }
    `;
    $('head').append(`<style>${blurStyle}</style>`);

    // Hover trigger for blur
    $(document).on('mouseenter', '.has-dropdown', function () {
      $('body').addClass('blur-active');
      positionBlurOverlay();
    }).on('mouseleave', '.has-dropdown', function () {
      $('body').removeClass('blur-active');
      positionBlurOverlay();
    });

    // Auto trigger blur when aa-Panel is visible
    if (window.MutationObserver) {
      const aaObserver = new MutationObserver(() => {
        if ($('.aa-Panel').length) {
          $('body').addClass('blur-active');
        } else {
          $('body').removeClass('blur-active');
        }
        positionBlurOverlay();
      });
      aaObserver.observe(document.body, { childList: true, subtree: true });
    }

    /* ========================
       🌀 Owl Carousel 2-Finger Swipe
    ======================== */
    const $carousels = $('.owl-carousel');

    $carousels.each(function () {
      const $carousel = $(this);
      let startX = 0;
      let isTwoFinger = false;
      let hasSwiped = false;
      const threshold = 120;
      const lockDuration = 250;
      const transitionSpeed = 400;

      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches;
        if (touches.length === 2) {
          isTwoFinger = true;
          startX = (touches[0].clientX + touches[1].clientX) / 2;
          hasSwiped = false;
        } else {
          isTwoFinger = false;
        }
      });

      $carousel.on('touchmove', function (e) {
        if (!isTwoFinger || hasSwiped) return;
        const touches = e.originalEvent.touches;
        if (touches.length !== 2) return;

        const currentX = (touches[0].clientX + touches[1].clientX) / 2;
        const deltaX = currentX - startX;

        if (Math.abs(deltaX) > threshold) {
          if (deltaX > 0) {
            $carousel.trigger('prev.owl.carousel', [transitionSpeed]);
          } else {
            $carousel.trigger('next.owl.carousel', [transitionSpeed]);
          }
          hasSwiped = true;
          e.preventDefault();

          setTimeout(() => {
            hasSwiped = false;
            isTwoFinger = false;
          }, lockDuration);
        }
      });

      $carousel.on('touchend touchcancel', function () {
        isTwoFinger = false;
      });

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

    /* ========================
       👁️ Hide Facelift Dropdown when AA Panel is active
    ======================== */
    const $dropdown = $('.facelift-dropdown-container');
    if ($dropdown.length) {
      function toggleDropdown() {
        if ($('.aa-Panel').length) {
          $dropdown.hide();
        } else {
          $dropdown.show();
        }
      }
      toggleDropdown();
      setInterval(toggleDropdown, 300);
    }

    /* ========================
       ✅ Rheostat Tooltip Boundary Fix
    ======================== */
    function limitRheostatTooltips() {
      function init() {
        const $slider = $('.ais-RangeSlider');
        if (!$slider.length) {
          setTimeout(init, 500);
          return;
        }

        const $handles = $slider.find('.rheostat-handle');

        function adjustTooltips() {
          const sliderRect = $slider[0].getBoundingClientRect();
          $handles.each(function () {
            const $handle = $(this);
            const $tooltip = $handle.find('.rheostat-tooltip');
            if ($tooltip.length) {
              const handleRect = $handle[0].getBoundingClientRect();
              const tooltipRect = $tooltip[0].getBoundingClientRect();
              const tooltipLeft = handleRect.left + handleRect.width / 2 - tooltipRect.width / 2;
              const minLeft = sliderRect.left;
              const maxLeft = sliderRect.right - tooltipRect.width;
              let newLeft = tooltipLeft;
              if (tooltipLeft < minLeft) newLeft = minLeft;
              if (tooltipLeft > maxLeft) newLeft = maxLeft;
              const offsetLeft = newLeft - handleRect.left;
              $tooltip.css({ position: 'absolute', left: offsetLeft + 'px', transform: 'translateX(0)' });
            }
          });
        }

        adjustTooltips();
        $(window).on('resize', adjustTooltips);
        const observer = new MutationObserver(adjustTooltips);
        $handles.each(function () {
          observer.observe(this, { attributes: true, attributeFilter: ['style'] });
        });
        $(document).on('pointermove mousemove touchmove', adjustTooltips);
      }
      init();
    }

    limitRheostatTooltips();

    /* ========================
       === RIBBON LOGIC
    ======================== */
    function toggleRibbonVisibility() {
      const $ribbons = $('.aa-Item .ribbon-digideals');
      let $input = $('#autocomplete-0-input');
      if (!$input.length) $input = $('.aa-Panel').find('input').first();
      let val = '';
      if ($input && $input.length) {
        val = String($input.val() || '').trim();
      }
      $ribbons.css('visibility', val === '' ? 'hidden' : 'visible');
    }

    setTimeout(toggleRibbonVisibility, 150);
    $(document).on('input', '#autocomplete-0-input', toggleRibbonVisibility);
    $(document).on('input', '.aa-Panel input', toggleRibbonVisibility);
    const ribbonObserver = new MutationObserver(() => toggleRibbonVisibility());
    ribbonObserver.observe(document.body, { childList: true, subtree: true });
    const checkInterval = setInterval(toggleRibbonVisibility, 500);
    setTimeout(() => clearInterval(checkInterval), 15000);
  });
});
