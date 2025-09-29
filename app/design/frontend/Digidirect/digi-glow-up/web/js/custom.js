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
       🛑 PDP: Disable Default Minicart and Use Custom
    ======================== */
    if ($('body').hasClass('catalog-product-view')) {
      const $defaultMinicart = $('#minicart-content-wrapper').closest('aside.modal-popup');
      const $customMinicart = $('#custom-minicart-wrapper');

      // Hide default minicart immediately
      $defaultMinicart.hide();

      // Disable default toggle
      $(document).off('click', '[data-role="minicart-toggle"]');

      // Function to populate custom minicart with KO content
      function populateCustomMinicart() {
        const $originalMiniCart = $('#mini-cart');
        if ($originalMiniCart.length) {
          // Clear previous content
          $customMinicart.empty();

          // Create KO container
          const $koContainer = $('<ol id="mini-cart" class="minicart-items"></ol>');
          $customMinicart.append($koContainer);

          // Apply KO bindings using the default cart model
          if (window.ko && window.checkout && window.checkout.cart) {
            ko.cleanNode($koContainer[0]);
            ko.applyBindings(window.checkout.cart, $koContainer[0]);
          }
        }
      }

      // Populate initially
      populateCustomMinicart();

      // Bind custom toggle
      $('[data-role="minicart-toggle"]').on('click', function (e) {
        e.preventDefault();
        populateCustomMinicart();
        $customMinicart.toggle();
      });

      // Show custom minicart on Add to Cart
      $(document).on('click', '.action.tocart, .product-add-to-cart', function () {
        populateCustomMinicart();
        $customMinicart.show();
      });

      // Keep default minicart hidden
      if (window.MutationObserver) {
        const observerPDP = new MutationObserver(function () {
          $defaultMinicart.hide();
        });
        observerPDP.observe(document.body, { childList: true, subtree: true });
      }
    }
  });
});
