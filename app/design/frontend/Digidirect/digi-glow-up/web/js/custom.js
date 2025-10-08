define([
  'jquery',
  'ko',
  'uiRegistry',
  'Magento_Ui/js/core/app',
  'Magento_Customer/js/customer-data',
], function ($, ko, registry, uiApp, customerData) {
  'use strict'

  $(function () {
    /* ========================
       ✅ Sticky Header (with placeholder)
    ======================== */
    const $header = $('.header.content')
    const $placeholder = $('<div class="header-placeholder"></div>')
    $placeholder.hide()
    $header.after($placeholder)

    let stickyPoint = 0
    let isSticky = false

    function recalcStickyPoint() {
      if (!isSticky && $header.length) stickyPoint = $header.offset().top
    }

    function positionAAPanel() {
      const $aaPanel = $('.aa-Panel')
      if ($aaPanel.length && isSticky) {
        const headerHeight = $header.outerHeight()
        $aaPanel.addClass('is-sticky').css('top', headerHeight + 'px')
      }
    }

    function removeAAPanelSticky() {
      const $aaPanel = $('.aa-Panel')
      if ($aaPanel.length) $aaPanel.removeClass('is-sticky').css('top', '')
    }

    function setSticky(active) {
      if (active && !isSticky) {
        $placeholder.height($header.outerHeight()).show()
        $header.addClass('is-sticky')
        isSticky = true
        positionAAPanel()
      } else if (!active && isSticky) {
        $header.removeClass('is-sticky')
        isSticky = false
        $placeholder.hide()
        removeAAPanelSticky()
      }
    }

    function updateSticky() {
      const scrollTop = $(window).scrollTop()
      setSticky(scrollTop >= stickyPoint)
    }

    setTimeout(() => {
      recalcStickyPoint()
      updateSticky()
    }, 300)

    $(window).on('scroll', updateSticky)
    $(window).on('resize', function () {
      recalcStickyPoint()
      updateSticky()
    })

    if (window.MutationObserver) {
      const observer = new MutationObserver(() => {
        setTimeout(() => {
          recalcStickyPoint()
          updateSticky()
          positionAAPanel()
        }, 200)
      })
      observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style'],
      })
    }

    /* ========================
       🧊 Global Blur Overlay
    ======================== */
    const $blurOverlay = $('<div class="global-blur-overlay"></div>')
    if (!$('.global-blur-overlay').length) $('body').append($blurOverlay)

    function positionBlurOverlay() {
      const $overlay = $('.global-blur-overlay')
      const $main = $('#maincontent')
      if (!$main.length) return

      const mainOffset = $main.offset().top
      const documentHeight = Math.max(
        $(document).height(),
        $('body').prop('scrollHeight')
      )
      const height = documentHeight - mainOffset

      if ($('body').hasClass('blur-active')) {
        $overlay.css({
          position: 'absolute',
          top: mainOffset + 'px',
          left: 0,
          width: '100%',
          height: height + 'px',
        })
      } else {
        $overlay.css({ height: '0' })
      }
    }

    positionBlurOverlay()
    $(window).on('resize scroll', positionBlurOverlay)

    if (window.MutationObserver) {
      const blurObserver = new MutationObserver(() => positionBlurOverlay())
      blurObserver.observe(document.body, { childList: true, subtree: true })
    }

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
    `
    $('head').append(`<style>${blurStyle}</style>`)

    $(document)
      .on('mouseenter', '.has-dropdown', function () {
        $('body').addClass('blur-active')
        positionBlurOverlay()
      })
      .on('mouseleave', '.has-dropdown', function () {
        $('body').removeClass('blur-active')
        positionBlurOverlay()
      })

    if (window.MutationObserver) {
      const aaObserver = new MutationObserver(() => {
        if ($('.aa-Panel').length) $('body').addClass('blur-active')
        else $('body').removeClass('blur-active')
        positionBlurOverlay()
      })
      aaObserver.observe(document.body, { childList: true, subtree: true })
    }

    /* ========================
       🛒 AJAX Add to Cart + Minicart
    ======================== */
    $(document).on(
      'submit',
      'form[data-role="tocart-form"], #product_addtocart_form',
      function (e) {
        e.preventDefault()
        const $form = $(this)
        const formData = new FormData($form[0])
        const actionUrl = $form.attr('action')

        $.ajax({
          url: actionUrl,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          showLoader: true,
          success: function (response) {
            customerData.invalidate(['cart'])
            customerData.reload(['cart'], true)
            $('body').trigger('processStop')

            setTimeout(function () {
              const $showCart = $('[data-block="minicart"]').find(
                '.action.showcart'
              )
              if ($showCart.length) {
                $showCart.trigger('click')
              } else {
                $('[data-block="minicart"]').trigger('click')
              }
              updateMinicartOverlay()
            }, 500)
          },
          error: function (err) {
            console.error('Add to cart failed', err)
            $('body').trigger('processStop')
          },
        })
      }
    )

    function updateMinicartOverlay() {
      const $minicart = $('.block-minicart[data-role="dropdownDialog"]')
      const $headerMenu = $('.ruby-menu-demo-header')
      const $miniOverlay = $('.minicart-overlay')

      const isVisible =
        $minicart.length &&
        $minicart.is(':visible') &&
        $minicart.css('display') !== 'none'

      if (isVisible) {
        if ($headerMenu.length) $headerMenu.css('z-index', 0)
        if ($miniOverlay.length) $miniOverlay.css('display', 'block')
      } else {
        if ($headerMenu.length) $headerMenu.css('z-index', '')
        if ($miniOverlay.length) $miniOverlay.css('display', 'none')
      }
    }

    if (window.MutationObserver) {
      const miniObserver = new MutationObserver(() => updateMinicartOverlay())
      miniObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['style', 'class'],
      })
    }

    const miniInterval = setInterval(updateMinicartOverlay, 300)
    $(window).on('unload beforeunload', function () {
      clearInterval(miniInterval)
    })

    /* ========================
       🌀 Owl Carousel 2-Finger Swipe
    ======================== */
    const $carousels = $('.owl-carousel')

    $carousels.each(function () {
      const $carousel = $(this)
      let startX = 0
      let isTwoFinger = false
      let hasSwiped = false
      const threshold = 120
      const lockDuration = 250
      const transitionSpeed = 400

      $carousel.on('touchstart', function (e) {
        const touches = e.originalEvent.touches
        if (touches.length === 2) {
          isTwoFinger = true
          startX = (touches[0].clientX + touches[1].clientX) / 2
          hasSwiped = false
        } else {
          isTwoFinger = false
        }
      })

      $carousel.on('touchmove', function (e) {
        if (!isTwoFinger || hasSwiped) return
        const touches = e.originalEvent.touches
        if (touches.length !== 2) return

        const currentX = (touches[0].clientX + touches[1].clientX) / 2
        const deltaX = currentX - startX

        if (Math.abs(deltaX) > threshold) {
          if (deltaX > 0) {
            $carousel.trigger('prev.owl.carousel', [transitionSpeed])
          } else {
            $carousel.trigger('next.owl.carousel', [transitionSpeed])
          }
          hasSwiped = true
          e.preventDefault()

          setTimeout(() => {
            hasSwiped = false
            isTwoFinger = false
          }, lockDuration)
        }
      })

      $carousel.on('touchend touchcancel', function () {
        isTwoFinger = false
      })

      $carousel.on('wheel', function (e) {
        const event = e.originalEvent
        if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
          e.preventDefault()
          if (hasSwiped) return
          hasSwiped = true

          if (event.deltaX > 0) {
            $carousel.trigger('next.owl.carousel', [transitionSpeed])
          } else {
            $carousel.trigger('prev.owl.carousel', [transitionSpeed])
          }

          setTimeout(() => {
            hasSwiped = false
          }, lockDuration)
        }
      })
    })

    /* ========================
       🔍 Update Autocomplete Header
    ======================== */
    $(document).on('keyup', '#autocomplete-0-input', function () {
      const query = $(this).val().trim()
      setTimeout(function () {
        const $headerEl = $(
          '.aa-Source[data-autocomplete-source-id="products"] .aa-SourceHeader p'
        )
        if ($headerEl.length) {
          $headerEl.text(
            query.length
              ? `Results for "${query}"`
              : 'Top Selling Products'
          )
        }
      }, 100)
    })

    /* ========================
       🛒 Minicart Modal Add Class
    ======================== */
    const observer2 = new MutationObserver(function () {
      const $minicartModal = $(
        'aside.modal-popup .modal-content #minicart-content-wrapper'
      ).closest('aside.modal-popup')
      if ($minicartModal.length && !$minicartModal.hasClass('minicart-modal')) {
        $minicartModal.addClass('minicart-modal')
      }
    })
    observer2.observe(document.body, { childList: true, subtree: true })

    /* ========================
       👁️ Hide Facelift Dropdown when AA Panel active
    ======================== */
    const $dropdown = $('.facelift-dropdown-container')
    if ($dropdown.length) {
      function toggleDropdown() {
        if ($('.aa-Panel').length) $dropdown.hide()
        else $dropdown.show()
      }
      toggleDropdown()
      setInterval(toggleDropdown, 300)
    }

    /* ========================
       ✅ Rheostat Tooltip Boundary Fix
    ======================== */
    function limitRheostatTooltips() {
      function init() {
        const $slider = $('.ais-RangeSlider')
        if (!$slider.length) {
          setTimeout(init, 500)
          return
        }

        const $handles = $slider.find('.rheostat-handle')

        function adjustTooltips() {
          const sliderRect = $slider[0].getBoundingClientRect()
          $handles.each(function () {
            const $handle = $(this)
            const $tooltip = $handle.find('.rheostat-tooltip')
            if ($tooltip.length) {
              const handleRect = $handle[0].getBoundingClientRect()
              const tooltipRect = $tooltip[0].getBoundingClientRect()
              const tooltipLeft =
                handleRect.left + handleRect.width / 2 - tooltipRect.width / 2
              const minLeft = sliderRect.left
              const maxLeft = sliderRect.right - tooltipRect.width
              let newLeft = tooltipLeft
              if (tooltipLeft < minLeft) newLeft = minLeft
              if (tooltipLeft > maxLeft) newLeft = maxLeft
              const offsetLeft = newLeft - handleRect.left
              $tooltip.css({
                position: 'absolute',
                left: offsetLeft + 'px',
                transform: 'translateX(0)',
              })
            }
          })
        }

        adjustTooltips()
        $(window).on('resize', adjustTooltips)
        const observer = new MutationObserver(adjustTooltips)
        $handles.each(function () {
          observer.observe(this, {
            attributes: true,
            attributeFilter: ['style'],
          })
        })
        $(document).on('pointermove mousemove touchmove', adjustTooltips)
      }
      init()
    }
    limitRheostatTooltips()

    /* ========================
       === RIBBON LOGIC
    ======================== */
    function toggleRibbonVisibility() {
      const $ribbons = $('.aa-Item .ribbon-digideals')
      let $input = $('#autocomplete-0-input')
      if (!$input.length) $input = $('.aa-Panel').find('input').first()
      let val = ''
      if ($input && $input.length) {
        val = String($input.val() || '').trim()
      }
      $ribbons.css('visibility', val === '' ? 'hidden' : 'visible')
    }

    setTimeout(toggleRibbonVisibility, 150)
    $(document).on('input', '#autocomplete-0-input', toggleRibbonVisibility)
    $(document).on('input', '.aa-Panel input', toggleRibbonVisibility)
    const ribbonObserver = new MutationObserver(() => toggleRibbonVisibility())
    ribbonObserver.observe(document.body, { childList: true, subtree: true })
    const checkInterval = setInterval(toggleRibbonVisibility, 500)
    setTimeout(() => clearInterval(checkInterval), 15000)

    /* ========================
       📱 Mobile Menu Overlay Toggle
    ======================== */
    const $mobileMenu = $('.mobile-menu')
    const $mobileMenuToggle = $('.mobile-menu-icon')

    if ($mobileMenu.length && $mobileMenuToggle.length) {
      // Ensure initial state hidden
      $mobileMenu.removeClass('active')
      $('body').removeClass('menu-open')

      $mobileMenuToggle.on('click', function (e) {
        e.preventDefault()
        const isActive = $mobileMenu.toggleClass('active').hasClass('active')
        $('body').toggleClass('menu-open', isActive)
        $mobileMenuToggle.attr('aria-expanded', isActive)
      })

      // Optional: close when clicking outside or pressing ESC
      $(document).on('click', function (e) {
        if (
          $mobileMenu.hasClass('active') &&
          !$(e.target).closest('.mobile-menu, .mobile-menu-icon').length
        ) {
          $mobileMenu.removeClass('active')
          $('body').removeClass('menu-open')
          $mobileMenuToggle.attr('aria-expanded', false)
        }
      })

      $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $mobileMenu.hasClass('active')) {
          $mobileMenu.removeClass('active')
          $('body').removeClass('menu-open')
          $mobileMenuToggle.attr('aria-expanded', false)
        }
      })
    }
  })
})
