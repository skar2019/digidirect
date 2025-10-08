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
        📱 Mobile Menu Overlay + Sliding Submenu (Apple Style)
     ======================== */
     const $mobileMenu = $('.mobile-menu')
     const $mobileMenuToggle = $('.mobile-menu-icon')

     if ($mobileMenu.length && $mobileMenuToggle.length) {
       // Add close button dynamically if not exists
       if (!$mobileMenu.find('.mobile-menu-close').length) {
         $mobileMenu.prepend(
           '<button class="mobile-menu-close" aria-label="Close menu">×</button>'
         )
       }

       $mobileMenu.removeClass('active')
       $('body').removeClass('menu-open')

       // ✅ Open menu
       $mobileMenuToggle.on('click', function (e) {
         e.preventDefault()
         $mobileMenu.addClass('active')
         $('body').addClass('menu-open')
         $mobileMenuToggle.attr('aria-expanded', true)
       })

       // ✅ Close menu
       $(document).on('click', '.mobile-menu-close', function () {
         $mobileMenu.removeClass('active')
         $('body').removeClass('menu-open')
         $mobileMenuToggle.attr('aria-expanded', false)

         // Reset all levels
         $mobileMenu.find('.menu-level').removeClass('active').css('left', '100%')
         $mobileMenu.find('.level-1').addClass('active').css('left', '0')
       })

       // ✅ ESC key
       $(document).on('keydown', function (e) {
         if (e.key === 'Escape' && $mobileMenu.hasClass('active')) {
           $mobileMenu.removeClass('active')
           $('body').removeClass('menu-open')
           $mobileMenuToggle.attr('aria-expanded', false)

           // Reset levels
           $mobileMenu.find('.menu-level').removeClass('active').css('left', '100%')
           $mobileMenu.find('.level-1').addClass('active').css('left', '0')
         }
       })

       // ✅ Navigate forward
       $mobileMenu.on('click', '.menu-item.has-children > .menu-link', function (e) {
         e.preventDefault()
         const $submenu = $(this).siblings('.menu-level')
         const $current = $(this).closest('.menu-level')

         if ($submenu.length) {
           $current.animate({ left: '-100%' }, 300).removeClass('active')
           $submenu.css('left', '100%').addClass('active').animate({ left: '0' }, 300)
         }
       })

       // ✅ Navigate backward
       $mobileMenu.on('click', '.menu-back', function (e) {
         e.preventDefault()
         const $current = $(this).closest('.menu-level')
         const $parent = $current.closest('.menu-item').closest('.menu-level')

         $current.animate({ left: '100%' }, 300).removeClass('active')
         $parent.addClass('active').animate({ left: '0' }, 300)
       })
     }

    }
  })
})
