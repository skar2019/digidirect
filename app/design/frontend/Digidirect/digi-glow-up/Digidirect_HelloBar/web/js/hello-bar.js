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
      backdrop.style.transition = 'left 0.3s ease';
      backdrop.style.left = `${activeTab.offsetLeft}px`;
      backdrop.style.width = `${activeTab.offsetWidth}px`;
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
      let startX = 0
      let isDragging = false
      let swipeEnabled = false

      slidesContainer.addEventListener('pointerdown', function (e) {
          if (e.target.closest('a')) {
              swipeEnabled = false
              return
          }

          swipeEnabled = true
          startX = e.clientX
          isDragging = false
      })

      slidesContainer.addEventListener('pointermove', function (e) {
          if (!swipeEnabled) return

          const diffX = e.clientX - startX
          if (Math.abs(diffX) > 10) {
              isDragging = true
          }
      })

      slidesContainer.addEventListener('pointerup', function (e) {
          if (!swipeEnabled || !isDragging) return

          const diffX = e.clientX - startX
          const threshold = 50

          if (Math.abs(diffX) > threshold) {
              switchSlide(
                  diffX < 0
                      ? (currentIndex + 1) % slides.length
                      : (currentIndex - 1 + slides.length) % slides.length
              )
              stopAutoMoveTemporarily()
          }
      })

      slidesContainer.addEventListener('pointercancel', function () {
          swipeEnabled = false
          isDragging = false
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
