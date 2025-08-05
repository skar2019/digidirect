require(['jquery'], function($) {
    $(document).ready(function() {
        var slides = $('.hellobar-slide');
        var current = 0;

        function showSlide(index) {
            slides.removeClass('active').eq(index).addClass('active');
            $('.hellobar-dot').removeClass('active').eq(index).addClass('active');
        }

        function nextSlide() {
            current = (current + 1) % slides.length;
            showSlide(current);
        }

        slides.eq(current).addClass('active');

        var dotsHtml = '';
        slides.each(function (index) {
            dotsHtml += `<span class="hellobar-dot" data-index="${index}"></span>`;
        });
        $('.hellobar-slider').append(`<div class="hellobar-dots">${dotsHtml}</div>`);
        $('.hellobar-dot').eq(0).addClass('active');

        $('.hellobar-slider').on('click', '.hellobar-dot', function() {
            current = parseInt($(this).data('index'));
            showSlide(current);
        });

        //setInterval(nextSlide, 2000);
        setTimeout(() => {
            setInterval(nextSlide, 4000);
        }, 2000);
    });
});
