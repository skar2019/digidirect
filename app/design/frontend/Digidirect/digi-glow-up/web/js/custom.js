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
   ✅ Sticky Header (Self-correcting)
======================== */
const $header = $('.header.content')
let $placeholder = $('.header-placeholder')

if (!$placeholder.length) {
  $placeholder = $('<div class="header-placeholder"></div>')
  $header.after($placeholder)
}

let stickyPoint = 0
let isSticky = false
let lastTop = 0
let stableCounter = 0

function recalcStickyPoint() {
  if (!isSticky && $header.length) {
    stickyPoint = $header.offset().top
  }
}

function setSticky(active) {
  if (active && !isSticky) {
    $placeholder.height($header.outerHeight()).show()
    $header.addClass('is-sticky')
    isSticky = true
  } else if (!active && isSticky) {
    $header.removeClass('is-sticky')
    isSticky = false
    $placeholder.hide()
  }
}

function updateSticky() {
  const scrollTop = $(window).scrollTop()
  setSticky(scrollTop >= stickyPoint)
}

/* 🧠 Continuous layout stabilization check */
function watchLayoutStability() {
  const currentTop = $header.offset().top
  if (currentTop === lastTop) {
    stableCounter++
  } else {
    stableCounter = 0
    lastTop = currentTop
  }

  if (stableCounter < 10) {
    // not stable yet → keep checking every frame
    requestAnimationFrame(watchLayoutStability)
  } else {
    // stable → recalc sticky safely
    recalcStickyPoint()
    updateSticky()
  }
}

$(window).on('load', () => {
  // wait for scroll restore + first layout
  setTimeout(() => {
    requestAnimationFrame(() => {
      recalcStickyPoint()
      updateSticky()
      watchLayoutStability()
    })
  }, 400)
})

$(window).on('scroll resize', () => {
  recalcStickyPoint()
  updateSticky()
})




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
    // Mouse enters ruby-menu-mega-blog but skip ones with .just-link
    .on('mouseenter', '.ruby-menu-mega-blog:not(.just-link)', function () {
      $('body').addClass('blur-active')
      positionBlurOverlay()
    })
    // Mouse leaves that same element
    .on('mouseleave', '.ruby-menu-mega-blog:not(.just-link)', function () {
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
    
//    /* ========================
//        💫 Blur Entire Page Except #pa-welcome-back
//     ======================== */
//     function togglePageBlur() {
//       const isActive = $('#pa-welcome-back').hasClass('active')
//       $('body').toggleClass('welcome-blur-active', isActive)
//     }
//
//     // Observe class changes on #pa-welcome-back
//     if (window.MutationObserver) {
//       const paEl = document.getElementById('pa-welcome-back')
//       if (paEl) {
//         const observer = new MutationObserver(togglePageBlur)
//         observer.observe(paEl, { attributes: true, attributeFilter: ['class'] })
//       }
//     }
//
//     // Add styles for blur effect (excluding #pa-welcome-back)
//     const blurPageStyle = `
//       body.welcome-blur-active > *:not(#pa-welcome-back) {
//         filter: blur(12px);
//         -webkit-filter: blur(12px);
//         transition: filter 0.3s ease;
//         pointer-events: none;
//       }
//     `
//     $('head').append(`<style>${blurPageStyle}</style>`)

    /* ========================
       ✨ Sync #pa-welcome-back with Body Blur
    ======================== */
    function toggleWelcomeBackBlur() {
      const isActive = $('#pa-welcome-back').hasClass('active')
      $('body').toggleClass('blur-active', isActive)
      if (typeof positionBlurOverlay === 'function') positionBlurOverlay()
    }

    // Observe #pa-welcome-back for .active changes
    if (window.MutationObserver) {
      const paEl = document.getElementById('pa-welcome-back')
      if (paEl) {
        const observer = new MutationObserver(toggleWelcomeBackBlur)
        observer.observe(paEl, { attributes: true, attributeFilter: ['class'] })
      }
    }

    // Run once at load (in case it's already active)
    toggleWelcomeBackBlur()
    
    //Lock body when blur is active
    // Prevent all scroll-related events
//    function disableBodyScroll() {
//      $('body').addClass('scroll-locked')
//      window.addEventListener('wheel', preventScroll, { passive: false })
//      window.addEventListener('touchmove', preventScroll, { passive: false })
//      window.addEventListener('keydown', preventKeyScroll, { passive: false })
//    }
//
//    function enableBodyScroll() {
//      $('body').removeClass('scroll-locked')
//      window.removeEventListener('wheel', preventScroll, { passive: false })
//      window.removeEventListener('touchmove', preventScroll, { passive: false })
//      window.removeEventListener('keydown', preventKeyScroll, { passive: false })
//    }
//
//    function preventScroll(e) {
//      e.preventDefault()
//    }
//
//    function preventKeyScroll(e) {
//      // prevent arrow keys, space, PgUp, PgDn, etc.
//      const keys = [32, 33, 34, 35, 36, 37, 38, 39, 40]
//      if (keys.includes(e.keyCode)) e.preventDefault()
//    }
//
//    // Optional CSS (keeps scrollbar visible)
//    $('head').append(`
//      <style>
//        body.scroll-locked {
//          overflow-y: scroll; /* ✅ keep scrollbar visible */
//          position: fixed;
//          width: 100%;
//        }
//      </style>
//    `)
//
//    // Example usage with your blur:
//    function toggleWelcomeBackBlur() {
//      const isActive = $('#pa-welcome-back').hasClass('active')
//      $('body').toggleClass('blur-active', isActive)
//      if (isActive) disableBodyScroll()
//      else enableBodyScroll()
//      if (typeof positionBlurOverlay === 'function') positionBlurOverlay()
//    }

    /* ========================
        🌐 Reposition #pa-welcome-back
     ======================== */
     const $paWelcomeBack = $('#pa-welcome-back')
     if ($paWelcomeBack.length && !$paWelcomeBack.parent().hasClass('page-wrapper')) {
       $('.page-wrapper').before($paWelcomeBack)
     }

     /* ========================
        ❌ Close Welcome Back & Remove Blur
     ======================== */
     $(document).on('click', '#welcome-back-close', function () {
       $('#pa-welcome-back').removeClass('active')
       $('body').removeClass('blur-active')
       positionBlurOverlay() // keep your old blur overlay working
     })

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
   🌀 Owl Carousel 2-Finger Swipe (Smooth Apple-like)
======================== */
const $carousels = $('.owl-carousel')

$carousels.each(function () {
  const $carousel = $(this)
  let startX = 0
  let isTwoFinger = false
  let hasSwiped = false
  let isAtEdge = false
  const threshold = 50            // ⬅️ lower sensitivity (was 120)
  const lockDuration = 250
  const transitionSpeed = 600     // ⬅️ smoother animation
  const edgeElastic = 40          // ⬅️ how far to "indent" when hitting edge

  $carousel.on('touchstart', function (e) {
    const touches = e.originalEvent.touches
    if (touches.length === 2) {
      isTwoFinger = true
      startX = (touches[0].clientX + touches[1].clientX) / 2
      hasSwiped = false
      isAtEdge = false
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

    // detect if at the edge (no more items)
    const carouselData = $carousel.data('owl.carousel')
    const atFirst = carouselData.current() === 0
    const atLast = carouselData.current() === carouselData.maximum()

    // Elastic push visual
    if ((atFirst && deltaX > 0) || (atLast && deltaX < 0)) {
      const elastic = Math.min(Math.abs(deltaX) / 4, edgeElastic)
      $carousel.css('transform', `translateX(${deltaX > 0 ? elastic : -elastic}px)`)
      isAtEdge = true
      return
    }

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

    // Reset elastic bounce
    if (isAtEdge) {
      $carousel.css({
        transition: 'transform 0.3s cubic-bezier(0.25, 1, 0.5, 1)',
        transform: 'translateX(0)',
      })
      setTimeout(() => {
        $carousel.css('transition', '')
      }, 300)
      isAtEdge = false
    }
  })

  // Smooth horizontal wheel scrolling
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
const $mobileFooterMenuToggle = $('.footer-mobile-menu-icon') /* clint */

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
      !$(e.target).closest('.mobile-menu, .mobile-menu-icon, .footer-mobile-menu-icon').length
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

/* ========================
   📱 Apple-style Multi-Level Navigation
======================== */

const $menuContainer = $('.mobile-menu-content')
const $menuLevels = $menuContainer.find('.menu-level')
const $backBtn = $('.back-btn')
const $menuTitle = $('.mobile-menu-title')

let menuHistory = []

// Handle click to go to next level
$(document).on('click', '.menu-item[data-target]', function () {
  const target = $(this).data('target')
  const $currentLevel = $menuLevels.filter('.active')
  const $nextLevel = $menuLevels.filter(`[data-parent="${target}"]`)

  if ($nextLevel.length) {
    menuHistory.push($currentLevel)
    $currentLevel.removeClass('active').addClass('previous')
    $nextLevel.addClass('active')

    $menuTitle.text($(this).text())
    $backBtn.show()
  }
})

// Back button handler
$backBtn.on('click', function () {
  const $currentLevel = $menuLevels.filter('.active')
  const $prevLevel = menuHistory.pop()

  if ($prevLevel && $prevLevel.length) {
    $currentLevel.removeClass('active')
    $prevLevel.removeClass('previous').addClass('active')

    if (menuHistory.length === 0) {
      $menuTitle.text('Menu')
      $backBtn.hide()
    } else {
      const parentTarget = $prevLevel.data('parent') || 'Menu'
      $menuTitle.text(parentTarget.charAt(0).toUpperCase() + parentTarget.slice(1))
    }
  }
})

// Reset to root when menu closes
$(document).on('click', '.mobile-menu-close', function () {
  resetMenuToRoot()
})

function resetMenuToRoot() {
  $menuLevels.removeClass('active previous')
  $menuLevels.filter('[data-level="1"]').addClass('active')
  $menuTitle.text('Menu')
  $backBtn.hide()
  menuHistory = []
}

const $mobileMenuClose = $('.mobile-menu-close')

if ($mobileMenuClose.length) {
  $mobileMenuClose.on('click', function () {
    $mobileMenu.removeClass('active')
    $('body').removeClass('menu-open')
    $mobileMenuToggle.attr('aria-expanded', false)
  })
}

/* ========================
   🎯 Owl Nav Fixed to Screen Edges (Global)
======================== */
(function () {
  function moveAllNavsToBody() {
    $('.owl-carousel').each(function (index) {
      const $carousel = $(this)
      const $nav = $carousel.find('.owl-nav')
      if (!$nav.length || $nav.data('moved')) return

      $nav.data('moved', true)
      $('body').append($nav)

      $nav.css({
        position: 'fixed',
        inset: 0, // shorthand for top/right/bottom/left = 0
        width: '100%',
        height: '100%',
        pointerEvents: 'none', // ✅ let swipe/touch go through
        zIndex: 999,
      })

      $nav.find('button').css({
        pointerEvents: 'auto !important', // ✅ only buttons receive clicks
        position: 'fixed',
        borderRadius: '50%',
        backdropFilter: 'blur(10px)',
        background: 'rgba(255,255,255,0.7)',
        border: 'none',
        boxShadow: '0 4px 10px rgba(0,0,0,0.15)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        cursor: 'pointer',
        zIndex: 10000,
        padding: 0,
      })
    })

    updateNavPositions()
  }

  function updateNavPositions() {
    $('.owl-carousel').each(function (i) {
      const $carousel = $(this)
      const rect = this.getBoundingClientRect()
      const centerY = rect.top + rect.height / 2
      const $nav = $('.owl-nav').eq(i)
      const $prev = $nav.find('.owl-prev')
      const $next = $nav.find('.owl-next')
      const offset = 16

      const topValue = Math.max(44, Math.min(window.innerHeight - 44, centerY))

      if ($prev.length) {
        $prev.css({
          left: `${offset}px`,
          top: `${topValue}px`,
          transform: 'translateY(-50%)',
        })
      }

      if ($next.length) {
        $next.css({
          right: `${offset}px`,
          top: `${topValue}px`,
          transform: 'translateY(-50%)',
        })
      }

      const visible = rect.bottom > 0 && rect.top < window.innerHeight
      $prev.css('opacity', visible ? 0.5 : 0)
      $next.css('opacity', visible ? 0.5 : 0)
    })
  }

  $(window).on('scroll resize', updateNavPositions)

  const observer = new MutationObserver(() => moveAllNavsToBody())
  observer.observe(document.body, { childList: true, subtree: true })

  $(window).on('load', () => setTimeout(moveAllNavsToBody, 600))
})()

/* ========================
   🎯 Replace Carousel Nav Arrows (Owl + Slick) with SVGs
======================== */
const prevSVG = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" width="50" height="50">
  <path d="M21.559,12.062 L15.618,17.984 L21.5221,23.944 C22.105,24.533 22.1021,25.482 21.5131,26.065 C21.2211,26.355 20.8391,26.4999987 20.4571,26.4999987 C20.0711,26.4999987 19.6851,26.352 19.3921,26.056 L12.4351,19.034 C11.8531,18.446 11.8551,17.4999987 12.4411,16.916 L19.4411,9.938 C20.0261,9.353 20.9781,9.354 21.5621,9.941 C22.1471,10.528 22.1451,11.478 21.5591,12.062 Z"></path>
</svg>`

const nextSVG = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" width="50" height="50">
  <path d="M23.5587,16.916 C24.1447,17.4999987 24.1467,18.446 23.5647,19.034 L16.6077,26.056 C16.3147,26.352 15.9287,26.4999987 15.5427,26.4999987 C15.1607,26.4999987 14.7787,26.355 14.4867,26.065 C13.8977,25.482 13.8947,24.533 14.4777,23.944 L20.3818,17.984 L14.4408,12.062 C13.8548,11.478 13.8528,10.5279 14.4378,9.941 C15.0218,9.354 15.9738,9.353 16.5588,9.938 L23.5588,16.916 Z"></path>
</svg>`

function replaceCarouselArrows() {
  /* 🦉 Owl Carousel */
  $('.owl-carousel').each(function () {
    const $carousel = $(this)
    const $prev = $carousel.find('.owl-prev span[aria-label="Previous"]')
    const $next = $carousel.find('.owl-next span[aria-label="Next"]')
    if ($prev.length) $prev.replaceWith(prevSVG)
    if ($next.length) $next.replaceWith(nextSVG)
  })

  /* 🧊 Slick Slider (Magento PageBuilder) */
  $('.pagebuilder-slider.slick-initialized').each(function () {
    const $slider = $(this)
    const $prev = $slider.find('.slick-prev')
    const $next = $slider.find('.slick-next')

    // Replace content if not already an SVG
    if ($prev.length && !$prev.find('svg').length) $prev.html(prevSVG)
    if ($next.length && !$next.find('svg').length) $next.html(nextSVG)
  })
}

/* Run once on DOM ready and again after sliders initialize */
$(document).ready(function () {
  replaceCarouselArrows()
})

// Optional: If some sliders initialize dynamically later (Magento does this)
$(document).on('init reInit afterChange', '.pagebuilder-slider', function () {
  replaceCarouselArrows()
})


/* ========================
   🧩 Show aa-Panel only when it has content
======================== */
function toggleAaPanelVisibility() {
  const $panel = $('.aa-Panel')
  if (!$panel.length) return

  // Check if panel has visible content (items, suggestions, etc.)
  const hasContent = $panel.find('.aa-Item, .aa-Source, .aa-List').children().length > 0

  // Toggle visibility
  if (hasContent) {
    $panel.addClass('is-ready')
  } else {
    $panel.removeClass('is-ready')
  }
}

/* Observe aa-Panel changes */
const aaObserver = new MutationObserver(toggleAaPanelVisibility)
aaObserver.observe(document.body, { childList: true, subtree: true })

/* Initial check (for good measure) */
toggleAaPanelVisibility()


/* ========================
   ⚪ Owl Carousel – Sliding Active Dot Indicator (Round)
======================== */
$(document).on('initialized.owl.carousel', function (event) {
  const $carousel = $(event.target)
  const $dotsContainer = $carousel.find('.owl-dots')

  if ($dotsContainer.length && !$dotsContainer.find('.dot-indicator').length) {
    const $firstDot = $dotsContainer.find('.owl-dot').first()
    if ($firstDot.length) {
      const size = $firstDot.outerWidth()
      const offset = $firstDot.position().left
      $dotsContainer.append(`<span class="dot-indicator" style="
        position:absolute;
        top:0;
        left:${offset}px;
        width:${size}px;
        height:${size}px;
        border-radius:50%;
        background:#000;
        transform:translateX(0);
        transition:transform 0.4s cubic-bezier(0.4,0,0.2,1);
        z-index:2;
      "></span>`)
    }
  }
})

$(document).on('changed.owl.carousel', function (event) {
  const $carousel = $(event.target)
  const index = event.item.index || 0
  const $dots = $carousel.find('.owl-dot')
  const $indicator = $carousel.find('.dot-indicator')
  if (!$dots.length || !$indicator.length) return

  const $targetDot = $dots.eq(index)
  if (!$targetDot.length) return

  const moveX = $targetDot.position().left
  $indicator.css('transform', `translateX(${moveX}px)`)
})



/* ========================
   🩹 Keep aa-Panel perfectly aligned under Sticky Header
======================== */
function alignAaPanel() {
  const $panel = $('.aa-Panel')
  const $input = $('#autocomplete-0-input')
  const $header = $('.header.content')

  if (!$panel.length || !$input.length || !$header.length) return

  const inputOffset = $input.offset()
  const headerHeight = $header.outerHeight() || 0
  const scrollTop = $(window).scrollTop()
  const inputTop = inputOffset.top - scrollTop
  const newTop = $header.hasClass('is-sticky')
    ? headerHeight + 1
    : inputTop + $input.outerHeight() + 1

  const newLeft = inputOffset.left
  const newWidth = $input.outerWidth()

  if ($header.hasClass('is-sticky')) {
    $panel.css({
      position: 'fixed',
      top: `${newTop}px`,
      left: `${newLeft}px`,
      right: 'unset',
      zIndex: 10000,
      marginTop: 0,
    })
  } else {
    // When not sticky, revert to Algolia’s normal flow
    $panel.attr('style', '')
  }
}

// Reactive updates
$(window).on('scroll resize', alignAaPanel)
$(document).on('input focus', '#autocomplete-0-input', alignAaPanel)

// Observe DOM since Algolia dynamically replaces the panel
const aaStickObserver = new MutationObserver(alignAaPanel)
aaStickObserver.observe(document.body, { childList: true, subtree: true })


/* ========================
   🧩 Close aa-Panel on Blog Mega Menu Hover
======================== */
$(document)
  .on('mouseenter', '.ruby-menu-mega-blog.hasdropdown', function () {
    // Hide aa-Panel and reset input state
    const $panel = $('.aa-Panel')
    const $input = $('#autocomplete-0-input')

    if ($panel.length) {
      $panel.css({ visibility: 'hidden', opacity: 0 })
      setTimeout(() => $panel.remove(), 100) // optional: fully remove if Algolia re-renders later
    }

    // Also blur the input to fully deactivate Algolia autocomplete
    if ($input.length) $input.trigger('blur')

    // Make sure blur overlay updates correctly
    $('body').removeClass('blur-active')
  })
  .on('mouseleave', '.ruby-menu-mega-blog.hasdropdown', function () {
    // Nothing special — let aa-Panel reappear if user focuses input again
  })
  

/* ========================
   🖐️ Slick Slider – 2-Finger Swipe (Trackpad)
======================== */
$(document).on('wheel', '.pagebuilder-slider.slick-slider', function (e) {
  const event = e.originalEvent
  // detect horizontal gesture
  if (Math.abs(event.deltaX) > Math.abs(event.deltaY)) {
    e.preventDefault()
    const $slider = $(this)
    if (!$slider.hasClass('slick-initialized')) return

    if (event.deltaX > 0) {
      $slider.slick('slickNext')
    } else {
      $slider.slick('slickPrev')
    }
  }
})


/* ========================
   🌀 TCL Banner – Two-Finger Swipe Only (No CSS / No Click)
      ======================== 
*/
 $(function () {
    // Settings
    const SWIPE_THRESHOLD = 50    // px
    const LOCK_MS = 300          // prevent repeated triggers
    const WHEEL_MIN = 10         // wheel threshold to consider as horizontal swipe

    function initBanner($carousel) {
      if (!$carousel || !$carousel.length) return

      const slidesContainer = $carousel[0].querySelector('.tcl-banner__slides-container')
      const slides = Array.from($carousel[0].querySelectorAll('.tcl-banner__slide'))
      const dots = Array.from($carousel[0].querySelectorAll('.tds-tab'))

      if (!slidesContainer || !slides.length) return

      // find initial active index from dots (aria-selected) or first slide shown
      let activeIndex = 0
      const selectedDotIndex = dots.findIndex(d => d.getAttribute('aria-selected') === 'true')
      if (selectedDotIndex >= 0) activeIndex = selectedDotIndex

      let twoFinger = false
      let touchStartX = 0
      let locked = false

      function setActive(index) {
        index = ((index % slides.length) + slides.length) % slides.length
        slides.forEach((slide, i) => slide.classList.toggle('tcl-banner__slide--active', i === index))
        dots.forEach((dot, i) => dot.setAttribute('aria-selected', i === index ? 'true' : 'false'))
        activeIndex = index
      }

      // initialize visible slide
      setActive(activeIndex)

      // Helper to lock briefly after a swipe to avoid multiple triggers
      function lockTemporary() {
        locked = true
        setTimeout(() => {
          locked = false
        }, LOCK_MS)
      }

      // --- Touch handlers (two-finger) ---
      function onTouchStart(e) {
        const touches = e.touches || (e.originalEvent && e.originalEvent.touches)
        if (!touches) return
        if (touches.length === 2) {
          twoFinger = true
          touchStartX = (touches[0].clientX + touches[1].clientX) / 2
        } else {
          twoFinger = false
        }
      }

      function onTouchMove(e) {
        if (!twoFinger || locked) return
        const touches = e.touches || (e.originalEvent && e.originalEvent.touches)
        if (!touches || touches.length !== 2) return

        const touchEndX = (touches[0].clientX + touches[1].clientX) / 2
        const deltaX = touchEndX - touchStartX

        // horizontal swipe detection only
        if (Math.abs(deltaX) > SWIPE_THRESHOLD) {
          // prevent vertical scroll when we trigger a slide change
          if (e.cancelable) e.preventDefault()

          if (deltaX < 0) {
            // left -> next
            setActive((activeIndex + 1) % slides.length)
          } else {
            // right -> prev
            setActive((activeIndex - 1 + slides.length) % slides.length)
          }
          twoFinger = false
          lockTemporary()
        }
      }

      function onTouchEnd() {
        twoFinger = false
      }

      // Add non-passive listeners so we can call preventDefault()
      slidesContainer.addEventListener('touchstart', onTouchStart, { passive: true })
      slidesContainer.addEventListener('touchmove', onTouchMove, { passive: false })
      slidesContainer.addEventListener('touchend', onTouchEnd, { passive: true })
      slidesContainer.addEventListener('touchcancel', onTouchEnd, { passive: true })

      // --- Wheel/trackpad support for horizontal two-finger swipes ---
      // Use the carousel element so the listener is scoped
      function onWheel(e) {
        // Prefer deltaX if horizontal; ignore small moves
        const ev = e || window.event
        const absX = Math.abs(ev.deltaX || 0)
        const absY = Math.abs(ev.deltaY || 0)

        // require horizontal-dominant and over threshold
        if (absX > absY && absX > WHEEL_MIN && !locked) {
          // prevent page horizontal scroll
          if (e.cancelable) e.preventDefault()

          if ((ev.deltaX || 0) > 0) {
            // scrolled left -> go next
            setActive((activeIndex + 1) % slides.length)
          } else {
            // scrolled right -> prev
            setActive((activeIndex - 1 + slides.length) % slides.length)
          }
          lockTemporary()
        }
      }

      // add wheel listener directly (not through jQuery) and non-passive so preventDefault works
      slidesContainer.addEventListener('wheel', onWheel, { passive: false })

      // Optional: clicking the dots should update activeIndex (keep behavior consistent)
      dots.forEach((dot, i) => {
        dot.addEventListener('click', function (ev) {
          ev.preventDefault && ev.preventDefault()
          setActive(i)
          lockTemporary()
        })
      })
    }

    // Initialize all tcl-banner__carousel instances when DOM ready.
    // If carousels may be rendered later, observe the DOM too.
    function initAll() {
      const $carousels = $('.tcl-banner__carousel')
      $carousels.each(function () {
        initBanner($(this))
      })
    }

    initAll()

    // If pagebuilder or other scripts may insert banners after load, watch for additions
    if (window.MutationObserver) {
      const mo = new MutationObserver((mutations) => {
        for (const m of mutations) {
          if (m.addedNodes && m.addedNodes.length) {
            // cheap detection: any added node that contains the banner class
            const found = Array.from(m.addedNodes).some((n) => {
              return (n.nodeType === 1 && (n.matches('.tcl-banner__carousel') || n.querySelector && n.querySelector('.tcl-banner__carousel')))
            })
            if (found) {
              initAll()
              break
            }
          }
        }
      })
      mo.observe(document.body, { childList: true, subtree: true })
    }
  })

/* ========================
   🎯 Slick Dots Animated Backdrop 
======================== */
  $(document).ready(function () {
    function initBackdrop($slider) {
      const $dots = $slider.find('.slick-dots')
      if (!$dots.length) return

      // ensure container positioning
      $dots.css('position', 'relative')

      // inject backdrop once
      let $backdrop = $dots.find('.slick--animated-backdrop')
      if (!$backdrop.length) {
        $backdrop = $('<div class="slick--animated-backdrop"></div>')
        $dots.append($backdrop)
      }

      function moveBackdrop(animate = true) {
        const $activeLi = $dots.find('li.slick-active')
        if (!$activeLi.length) return

        const liOffset = $activeLi.position()?.left || 0
        const liWidth = $activeLi.outerWidth() || 0

        // temporarily disable transition for instant placement
        if (!animate) $backdrop.css('transition', 'none')

        $backdrop.css({
          transform: `translateX(${liOffset}px)`,
          width: liWidth + 'px',
        })

        // restore transition after first placement
        if (!animate) {
          setTimeout(() => {
            $backdrop.css('transition', '')
          }, 50)
        }
      }

      // ✅ Place backdrop instantly on first render
      setTimeout(() => moveBackdrop(false), 50)

      // then re-enable smooth motion for future changes
      $slider.on('afterChange', () => moveBackdrop(true))
      $(window).on('resize', () => moveBackdrop(true))

      // dot click updates backdrop visually even before slide change
      $dots.on('click', 'button', function () {
        setTimeout(() => moveBackdrop(true), 150)
      })
    }

    // Wait for Slick to initialize and dots to appear
    const checkSlick = setInterval(function () {
      const $sliders = $('.pagebuilder-slider.slick-initialized')
      if ($sliders.length && $sliders.find('.slick-dots').length) {
        clearInterval(checkSlick)
        $sliders.each(function () {
          initBackdrop($(this))
        })
      }
    }, 200)
  })


/* ========================
 🎯 Owl Dots Tesla-Style Backdrop (Rounded Highlight)
======================== */
$(document).ready(function () {
  function initOwlBackdrop($carousel) {
    const $dots = $carousel.find('.owl-dots')
    if (!$dots.length) return

    $dots.css('position', 'relative')

    // Ensure backdrop exists
    let $backdrop = $dots.find('.owl--animated-backdrop')
    if (!$backdrop.length) {
      $backdrop = $('<div class="owl--animated-backdrop"></div>')
      $dots.append($backdrop)
    }

    function moveBackdrop($dot, animate = true) {
      if (!$dot?.length) return

      const dotOffset = $dot.position()?.left || 0
      const dotWidth = $dot.outerWidth() || 0
      const dotHeight = $dot.outerHeight() || 0

      if (!animate) $backdrop.css('transition', 'none')

      // Center the backdrop behind the dot
      $backdrop.css({
        transform: `translateX(${dotOffset}px)`,
        width: `${dotWidth}px`,
        height: `${dotHeight}px`,
      })

      if (!animate) setTimeout(() => $backdrop.css('transition', ''), 50)
    }

    // Initial placement
    setTimeout(() => moveBackdrop($dots.find('.owl-dot.active'), false), 100)

    // On dot click → instant move
    $dots.on('click', '.owl-dot', function () {
      moveBackdrop($(this), true)
    })

    // On carousel slide change
    $carousel.on('changed.owl.carousel', function () {
      // Re-inject backdrop if Owl rebuilt the dots
      if (!$dots.find('.owl--animated-backdrop').length) {
        $dots.append($backdrop)
      }
      moveBackdrop($dots.find('.owl-dot.active'), true)
    })

    // On resize
    $(window).on('resize', function () {
      moveBackdrop($dots.find('.owl-dot.active'), true)
    })
  }

  // Wait for Owl to initialize
  const checkOwl = setInterval(() => {
    const $carousels = $('.owl-carousel.owl-loaded')
    if ($carousels.length && $carousels.find('.owl-dots').length) {
      clearInterval(checkOwl)
      $carousels.each(function () {
        initOwlBackdrop($(this))
      })
    }
  }, 200)
})

//Minicart Remove Confirmation Change Text

$(function () {
    // Watch for dynamically created confirm popups
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        $(mutation.addedNodes).each(function () {
          const $node = $(this)

          // Check if a confirm modal has appeared
          if (
            $node.hasClass('modal-popup') &&
            $node.hasClass('confirm') &&
            $node.find('.modal-content:contains("remove this item")').length
          ) {// ✳️ Change question text
            $node.find('.modal-content div').text(
              'Are you sure you would like to remove this item?'
            )
            // Update button labels
            $node
              .find('.action-secondary.action-dismiss span')
              .text('No, Keep It')
            $node
              .find('.action-primary.action-accept span')
              .text('Yes, Remove It')

            // Optional: focus No button
            $node.find('.action-secondary.action-dismiss').focus()
          }
        })
      })
    })

    // Observe the whole body for new modal popups
    observer.observe(document.body, { childList: true, subtree: true })
  })
  
  //Change Proceed To Checkout Text
  
  // Wait for minicart or cart page to load fully
  const checkoutBtnInterval = setInterval(function () {
    // 🛒 Mini cart button
    const $miniBtn = $('.action.primary.checkout, .checkout.methods .action.checkout');
    // 🛍️ Cart page button
    const $cartBtn = $('.cart-summary .checkout-methods-items .action.primary.checkout');

    if ($miniBtn.length || $cartBtn.length) {
      $miniBtn.text('Proceed to Checkout');
      $cartBtn.text('Proceed to Checkout');

      // Optionally also update the title attribute (for accessibility)
      $miniBtn.attr('title', 'Proceed to Checkout');
      $cartBtn.attr('title', 'Proceed to Checkout');

      clearInterval(checkoutBtnInterval);
    }
  }, 300);


  })
})
