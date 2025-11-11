require(['jquery'], function ($) {
  $(document).ready(function () {
    const tabs = document.querySelectorAll('.tds-tab')
    const slides = document.querySelectorAll('.tcl-banner__slide')
    const backdrop = document.querySelector('.tds--animated-backdrop')
    const slidesContainer = document.querySelector('.tcl-banner__slides-container')
    let currentIndex = 0

    let autoInterval
    let resumeTimeout

    // ---- Helper: Move backdrop to active dot ----
    function moveBackdrop() {
      const activeTab = document.querySelector('.tds-tab[aria-selected="true"]')
      if (!activeTab || !backdrop) return
      backdrop.style.transition = 'left 0.3s ease'
      backdrop.style.left = `${activeTab.offsetLeft}px`
      backdrop.style.width = `${activeTab.offsetWidth}px`
    }

    // ---- Helper: Switch active slide + dots ----
    function switchSlide(index) {
      if (index < 0) index = slides.length - 1
      if (index >= slides.length) index = 0
      currentIndex = index

      slides.forEach((slide, i) =>
        slide.classList.toggle('tcl-banner__slide--active', i === index)
      )

      tabs.forEach((tab, i) => {
        const isActive = i === index
        tab.classList.toggle('active', isActive)
        tab.setAttribute('aria-selected', isActive ? 'true' : 'false')
      })

      moveBackdrop()
    }

    // ---- Autoplay ----
    function startAutoMove() {
      clearInterval(autoInterval)
      autoInterval = setInterval(() => {
        currentIndex = (currentIndex + 1) % slides.length
        switchSlide(currentIndex)
      }, 4000)
    }

    function stopAutoMoveTemporarily() {
      clearInterval(autoInterval)
      clearTimeout(resumeTimeout)
      resumeTimeout = setTimeout(() => startAutoMove(), 2000)
    }

    // ---- Dots click ----
    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => {
        currentIndex = index
        switchSlide(index)
        stopAutoMoveTemporarily()
      })
    })

      // ---- Swipe detection ----
      let touchStartX = 0
      let touchEndX = 0

      slidesContainer.addEventListener('touchstart', function (e) {
          touchStartX = e.touches[0].clientX
      })

      slidesContainer.addEventListener('touchmove', function (e) {
          touchEndX = e.touches[0].clientX
      })

      slidesContainer.addEventListener('touchend', function () {
          const swipeDistance = touchEndX - touchStartX

          const threshold = 50

          if (Math.abs(swipeDistance) > threshold) {
              if (swipeDistance < 0) {
                  currentIndex = (currentIndex + 1) % slides.length
              } else {
                  currentIndex = (currentIndex - 1 + slides.length) % slides.length
              }
              switchSlide(currentIndex)
              stopAutoMoveTemporarily()
          }
      })

    // ---- Observer: detect external changes ----
    const observer = new MutationObserver(() => {
      const newIndex = Array.from(slides).findIndex((s) =>
        s.classList.contains('tcl-banner__slide--active')
      )
      if (newIndex !== -1 && newIndex !== currentIndex) {
        currentIndex = newIndex
        switchSlide(newIndex)
      }
    })

    observer.observe(slidesContainer, {
      attributes: true,
      subtree: true,
      attributeFilter: ['class']
    })

    // ---- Initialize ----
    switchSlide(currentIndex)
    moveBackdrop()
    startAutoMove()
  })
})
