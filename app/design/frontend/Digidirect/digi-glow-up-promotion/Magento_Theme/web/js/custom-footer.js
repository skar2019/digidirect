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
                $(this).find('.footer-black-gradient').css('opacity', '1').css('z-index', '1');
            }
        );

        if (window.location.pathname === '/') {
            document.querySelector('.home-footer-menu')?.classList.add('active');
        }

        if (window.location.pathname === '/customer/account/index/' || window.location.pathname === '/customer/account/index/') {
            document.querySelector('.account-footer-menu')?.classList.add('active');
        }


        const closeButtons = document.querySelectorAll('.mobile-menu-close, .mobile-services-close, .minicart-close, .close-popup');

        // Add click event listener to each close button
        closeButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove 'active' class from all footer nav items
                document.querySelectorAll('.footer-nav-item').forEach(navItem => {
                    navItem.classList.remove('active');
                });
            });
        });

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

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAccountPopup();
            closeAlgolia();

            if ($('.mobile-menu').hasClass('active')) {

                $('.mobile-menu-close').trigger('click');

            } else {

                $('.mobile-menu').addClass('active');
            }

        });

        $('.footer-search').on('click', function (e) {
            e.preventDefault(); // Prevent any default behavior

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAccountPopup();

            const input = document.querySelector('.aa-Input');
            if (!input) return;

            // Scroll to top
            window.scrollTo({ top: 0 });

            // Remove readonly IMMEDIATELY
            input.removeAttribute('readonly');

            // Focus IMMEDIATELY - this is critical for mobile keyboard
            input.focus();
            input.click();

            // Force cursor to end of input if there's existing text
            if (input.value) {
                const length = input.value.length;
                input.setSelectionRange(length, length);
            }
        });

        $('.showcart-footer').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .close-popup').trigger('click');

            closeAlgolia();

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });

            closeAccountPopup();

            $('.showcart').trigger('click');
        });



        $('#open-account-popup, .account-top-link-mobile').on('click', function(){

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAlgolia();
            $('#mobile-account-popup').addClass('active account');
            $('body').css('overflow', 'hidden');
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

            closeAlgolia();

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

        if (window.matchMedia("(max-width: 768px)").matches) {
            if ((window.location.pathname === '/customer/account'
                    || window.location.pathname === '/customer/account/'
                    || window.location.pathname === '/customer/account/index'
                    || window.location.pathname === '/customer/account/index/'
                )
                && window.showLoginOverlay) {
                $('#open-account-popup').trigger('click');

                window.showLoginOverlay = false;
            }
        }

        $('.goback').on('click', function() {
            $('#open-account-popup').trigger('click');
        });
    });

    function closeAlgolia() {
        // Close autocomplete cleanly - works on mobile and desktop
        if (window.algoliaAutocompleteInstance && typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
            // Use Algolia's built-in method to close properly
            window.algoliaAutocompleteInstance.setIsOpen(false);
            console.log('[Algolia] Autocomplete closed via setIsOpen ✅');

            // Blur the search input
            const input = document.querySelector('input[type="search"], .aa-Input');
            if (input) {
                input.blur();

                // Mobile keyboard dismissal
                if (document.activeElement === input) {
                    input.setAttribute('readonly', 'readonly');
                    setTimeout(function() {
                        input.removeAttribute('readonly');
                        input.blur();
                    }, 100);
                }
            }
        } else {
            console.warn('[Algolia] Autocomplete instance not available');
        }
    }

    function closeAccountPopup() {

        if (window.location.href.indexOf('/customer/') !== -1) {
            window.location.href = '/';
        } else {
            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAlgolia();
            $('#mobile-account-popup').removeClass('active');
            $('body').css('overflow', '');
        }

    }
});
