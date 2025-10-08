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

        document.querySelectorAll('.footer-nav-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all footer nav items
                document.querySelectorAll('.footer-nav-item').forEach(navItem => {
                    navItem.classList.remove('active');
                });

                // Add active class to the clicked item
                this.classList.add('active');
            });
        });

        $('#open-account-popup').on('click', function(){
            $('#mobile-account-popup').addClass('active');
            $('body').css('overflow', 'hidden');
        });

        $('.close-popup').on('click', function(){
            $('#mobile-account-popup').removeClass('active');
            $('body').css('overflow', '');
        });

        $('#mobile-account-popup').on('click', function(){
            window.location.href = '/customer/account/logout/';
        });

    });
});
