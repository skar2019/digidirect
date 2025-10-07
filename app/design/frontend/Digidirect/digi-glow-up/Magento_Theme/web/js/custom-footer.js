require(['jquery'], function($) {

    function toggleMobileMenu() {
        // Trigger Magento's mobile menu
        $('[data-action="toggle-nav"]').trigger('click');
    }

    // Make function globally available
    window.toggleMobileMenu = toggleMobileMenu;

    $(document).ready(function() {
        $('.page-footer').hover(
            function () {
              $(this).find('.footer-black-gradient').css('opacity', '0');
            },
            function () {
              $(this).find('.footer-black-gradient').css('opacity', '1');
            }
        );

        var currentUrl = window.location.pathname;
        $('.footer-nav-item').each(function() {
            var href = $(this).attr('href');
            if (href && currentUrl.indexOf(href) > -1 && href !== '/') {
                $('.footer-nav-item').removeClass('active');
                $(this).addClass('active');
            }
        });

    });
});
