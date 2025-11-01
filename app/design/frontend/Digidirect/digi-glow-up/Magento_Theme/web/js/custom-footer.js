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


        $('.footer-mobile-menu-icon').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close, .account-popup-header .close-popup').trigger('click');

            if ($('.mobile-menu').hasClass('active')) {

                $('.mobile-menu-close').trigger('click');

            } else {

                $('.mobile-menu').addClass('active');
            }


        });

        $('.footer-search').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close, .account-popup-header .close-popup').trigger('click');

            document.querySelector('.aa-Input').focus();
        });

        $('.showcart-footer').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close, .account-popup-header .close-popup').trigger('click');
            document.querySelector('.aa-Input')?.blur();

            $('.showcart').trigger('click');
        });



        $('#open-account-popup').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            document.querySelector('.aa-Input')?.blur();
            $('#mobile-account-popup').addClass('active account');
            $('body').css('overflow', 'hidden');
        });

        $('.account-popup-header .close-popup').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            document.querySelector('.aa-Input')?.blur();
            $('#mobile-account-popup').removeClass('active');
            $('body').css('overflow', '');
        });

        $('.account-popup-header .signout, .account-popup-header-in-pages .signout').on('click', function(){
            window.location.href = '/customer/account/logout/';
        });

        $('.account-popup-header-in-pages .close-popup').on('click', function(){
            window.location.href = '/';
        });

        $('[data-modal-trigger]').on('click', function() {
            var modalId = $(this).attr('data-modal-trigger');
            var $modal = $('[data-modal="' + modalId + '"]');

            $('.mobile-menu-close, .minicart-close').trigger('click');
            document.querySelector('.aa-Input')?.blur();

            $modal.addClass('active');
            $('body').addClass('modal-open');

            // Trigger animation
            setTimeout(function() {
                $modal.find('.mobile-modal-container').css('transform', 'translateY(0)');
            }, 10);


        });

        // Close modal
        $('[data-modal-close]').on('click', function() {
            var modalId = $(this).attr('data-modal-close');
            closeModal(modalId);
        });

        // Close on overlay click
        $('.mobile-modal-overlay').on('click', function() {
            var $modal = $(this).closest('.mobile-modal');
            var modalId = $modal.attr('data-modal');
            closeModal(modalId);
        });

        function closeModal(modalId) {
            var $modal = $('[data-modal="' + modalId + '"]');

            $modal.find('.mobile-modal-container').css('transform', 'translateY(100%)');

            setTimeout(function() {
                $modal.removeClass('active');
                $('body').removeClass('modal-open');
            }, 300);
        }

    });
});
