require(['jquery'], function($) {
    $(document).ready(function() {
        var slides = $('.hellobar-slide');
        var current = 0;
        var animationDuration = 2500;
        const delayBetweenSlides = 0;

        slides.eq(current).addClass('active');

        var dotsHtml = '';
        slides.each(function (index) {
            dotsHtml += `<span class="hellobar-dot" data-index="${index}"></span>`;
        });
        $('.hellobar-slider').append(`<div class="hellobar-dots">${dotsHtml}</div>`);
        $('.hellobar-dot').eq(0).addClass('active');

        $('.hellobar-slider').on('click', '.hellobar-dot', function() {
            var target = parseInt($(this).data('index'));
            if (target === current) return;

            slides.eq(current).removeClass('active animate-in-out');
            current = target;
            slides.eq(current).addClass('active');
            $('.hellobar-dot').removeClass('active').eq(current).addClass('active');
        });

        function nextSlide() {
            var currentSlide = slides.eq(current);
            currentSlide.addClass('animate-in-out');

            setTimeout(function () {
                currentSlide.removeClass('active animate-in-out');

                current = (current + 1) % slides.length;
                var next = slides.eq(current);
                next.addClass('active animate-in-out');

                $('.hellobar-dot').removeClass('active').eq(current).addClass('active');
            }, animationDuration);
        }

        setTimeout(function () {
            slides.eq(current).addClass('active animate-in-out');

            setInterval(nextSlide, animationDuration + delayBetweenSlides);
        }, 10);

    });
});
