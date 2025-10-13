require(['jquery'], function($) {
    $(document).ready(function() {
        /*var slides = $('.hellobar-slide');
        var current = 0;

        function showSlide(index) {
            slides.removeClass('active').eq(index).addClass('active');
            $('.hellobar-dot').removeClass('active').eq(index).addClass('active');
        }

        function nextSlide() {
            current = (current + 1) % slides.length;
            showSlide(current);
        }

        setTimeout(() => {
            showSlide(current);
            setInterval(nextSlide, 5000);
        }, 200);

        var dotsHtml = '';
        slides.each(function (index) {
            dotsHtml += `<span class="hellobar-dot" data-index="${index}"></span>`;
        });
        $('.hellobar-slider').append(`<div class="hellobar-dots">${dotsHtml}</div>`);

        $('.hellobar-slider').on('click', '.hellobar-dot', function() {
            current = parseInt($(this).data('index'));
            showSlide(current);
        });*/

        const tabs = document.querySelectorAll('.tds-tab');
        const slides = document.querySelectorAll('.tcl-banner__slide');
        const backdrop = document.querySelector('.tds--animated-backdrop');
        let currentIndex = 0;

        // Auto move interval variable
        let autoInterval;
        let resumeTimeout;

        // Move the backdrop under the selected dot
        function moveBackdrop() {
            const activeTab = document.querySelector('.tds-tab[aria-selected="true"]');
            backdrop.style.left = `${activeTab.offsetLeft}px`;
        }

        // Switch slide + dot
        function switchSlide(index) {
            slides.forEach(slide => slide.classList.remove('tcl-banner__slide--active'));
            slides[index].classList.add('tcl-banner__slide--active');

            tabs.forEach(tab => tab.setAttribute('aria-selected', 'false'));
            tabs[index].setAttribute('aria-selected', 'true');

            moveBackdrop();
        }

        // Start auto-moving
        function startAutoMove() {
            clearInterval(autoInterval);
            autoInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % slides.length;
                switchSlide(currentIndex);
            }, 3000);
        }

        // Stop auto-moving temporarily when user clicks
        function stopAutoMoveTemporarily() {
            clearInterval(autoInterval);
            clearTimeout(resumeTimeout);

            // Resume auto move after 10 seconds of inactivity
            resumeTimeout = setTimeout(() => {
                startAutoMove();
            }, 2000);
        }

        // On click: switch slide + stop temporarily
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                currentIndex = index;
                switchSlide(index);
                stopAutoMoveTemporarily();
            });
        });

        // Initial position and auto start
        window.addEventListener('DOMContentLoaded', () => {
            moveBackdrop();
            startAutoMove();
        });
    });
});
