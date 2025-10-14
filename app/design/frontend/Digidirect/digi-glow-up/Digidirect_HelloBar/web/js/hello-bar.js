require(['jquery'], function($) {
    $(document).ready(function() {
        const tabs = document.querySelectorAll('.tds-tab');
        const slides = document.querySelectorAll('.tcl-banner__slide');
        const backdrop = document.querySelector('.tds--animated-backdrop');
        let currentIndex = 0;

        let autoInterval;
        let resumeTimeout;

        function moveBackdrop() {
            const activeTab = document.querySelector('.tds-tab[aria-selected="true"]');
            backdrop.style.left = `${activeTab.offsetLeft}px`;
        }

        function switchSlide(index) {
            slides.forEach(slide => slide.classList.remove('tcl-banner__slide--active'));
            slides[index].classList.add('tcl-banner__slide--active');

            tabs.forEach(tab => tab.setAttribute('aria-selected', 'false'));
            tabs[index].setAttribute('aria-selected', 'true');

            moveBackdrop();
        }

        function startAutoMove() {
            clearInterval(autoInterval);
            autoInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                switchSlide(currentIndex);
            }, 3000);
        }

        function stopAutoMoveTemporarily() {
            clearInterval(autoInterval);
            clearTimeout(resumeTimeout);

            resumeTimeout = setTimeout(() => {
                startAutoMove();
            }, 2000);
        }

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                currentIndex = index;
                switchSlide(index);
                stopAutoMoveTemporarily();
            });
        });

        window.addEventListener('DOMContentLoaded', () => {
            moveBackdrop();
            startAutoMove();
        });
    });
});
