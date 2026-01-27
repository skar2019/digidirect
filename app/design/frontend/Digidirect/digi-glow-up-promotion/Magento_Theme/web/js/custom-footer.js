require(['jquery'], function($) {

    function toggleMobileMenu() {
        $('[data-action="toggle-nav"]').trigger('click');
    }

    window.toggleMobileMenu = toggleMobileMenu;

    $(document).ready(function() {
        // Cache DOM queries
        const $body = $('body');
        const $pageFooter = $('.page-footer');
        const $footerGradient = $('.footer-black-gradient');
        const $mobileAccountPopup = $('#mobile-account-popup');
        const $closeButtons = $('.mobile-menu-close, .mobile-services-close, .minicart-close, .close-popup');
        const $footerNavItems = $('.footer-nav-item');

        // Passive event listeners for better scroll performance
        const passiveSupported = checkPassiveSupport();

        // Footer hover (desktop only - remove on mobile)
        if (window.matchMedia("(min-width: 769px)").matches) {
            $pageFooter.hover(
                function() { $footerGradient.css('opacity', '0'); },
                function() { $footerGradient.css('opacity', '1').css('z-index', '1'); }
            );
        }

        // Active state management
        initializeActiveStates();

        // Event delegation for better performance
        setupEventDelegation();

        // Individual handlers
        setupFooterMenuIcon();
        setupFooterSearch();
        setupFooterCart();
        setupAccountPopup();
        setupModals();

        // Auto-open login overlay on account pages
        handleAutoLogin();

        // Chat from email link
        handleEmailChatLink();
    });

    // Check for passive event support
    function checkPassiveSupport() {
        let passive = false;
        try {
            const options = {
                get passive() {
                    passive = true;
                    return false;
                }
            };
            window.addEventListener("test", null, options);
            window.removeEventListener("test", null, options);
        } catch(err) {
            passive = false;
        }
        return passive;
    }

    function initializeActiveStates() {
        const path = window.location.pathname;

        if (path === '/') {
            document.querySelector('.home-footer-menu')?.classList.add('active');
        }

        if (path === '/customer/account/index/' || path === '/customer/account/index/') {
            document.querySelector('.account-footer-menu')?.classList.add('active');
        }
    }

    function setupEventDelegation() {
        const $footerNavItems = $('.footer-nav-item');
        const $closeButtons = $('.mobile-menu-close, .mobile-services-close, .minicart-close, .close-popup');

        // Close button handler
        $closeButtons.on('click', function() {
            requestAnimationFrame(() => {
                $footerNavItems.removeClass('active');
            });
        });

        // Footer nav item handler
        $footerNavItems.on('click', function() {
            const $this = $(this);
            requestAnimationFrame(() => {
                $footerNavItems.removeClass('active');
                $this.addClass('active');
            });
        });
    }

    function setupFooterMenuIcon() {
        $('.footer-mobile-menu-icon').on('click', function() {
            const $mobileMenu = $('.mobile-menu');

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAccountPopup();
            closeAlgolia();

            requestAnimationFrame(() => {
                if ($mobileMenu.hasClass('active')) {
                    $('.mobile-menu-close').trigger('click');
                } else {
                    $mobileMenu.addClass('active');
                }
            });
        });
    }

    function setupFooterSearch() {
        // Remove preventDefault so the natural touch can propagate
        $('.footer-search').on('touchstart', function(e) {
            e.stopPropagation(); // Stop it from bubbling, but don't prevent default

            const input = document.querySelector('.aa-Input');
            if (!input) {
                console.log('❌ Input not found');
                return;
            }

            console.log('✅ Redirecting touch to input');

            // Make sure Algolia autocomplete is open/visible first
            if (window.algoliaAutocompleteInstance && 
                typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
                window.algoliaAutocompleteInstance.setIsOpen(true);
            }

            // Close other menus
            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            
            if (window.location.href.indexOf('/customer/') === -1) {
                $('#mobile-account-popup').removeClass('active');
                $('body').css('overflow', '');
            }

            // Scroll to top
            window.scrollTo(0, 0);

            // Make input ready immediately
            input.removeAttribute('readonly');
            input.removeAttribute('disabled');
            input.style.pointerEvents = 'auto';

            // Get the original touch event
            const originalEvent = e.originalEvent;
            if (originalEvent && originalEvent.touches && originalEvent.touches.length > 0) {
                const touch = originalEvent.touches[0];
                
                // Create new touch events targeting the input
                const newTouchObj = {
                    identifier: touch.identifier,
                    target: input,
                    clientX: touch.clientX,
                    clientY: touch.clientY,
                    screenX: touch.screenX,
                    screenY: touch.screenY,
                    pageX: touch.pageX,
                    pageY: touch.pageY,
                    radiusX: touch.radiusX || 0,
                    radiusY: touch.radiusY || 0,
                    rotationAngle: touch.rotationAngle || 0,
                    force: touch.force || 1
                };

                try {
                    // Try to create a proper Touch object
                    const touchObj = new Touch(newTouchObj);
                    
                    // Redirect touch to input
                    const touchEvent = new TouchEvent(originalEvent.type, {
                        bubbles: true,
                        cancelable: true,
                        touches: [touchObj],
                        targetTouches: [touchObj],
                        changedTouches: [touchObj],
                        view: window
                    });
                    
                    input.dispatchEvent(touchEvent);
                } catch (err) {
                    console.log('Touch redirection failed:', err);
                    // Fallback to simple focus
                    input.focus();
                }
            }
        });

        // Also handle touchend
        $('.footer-search').on('touchend', function(e) {
            e.stopPropagation();

            const input = document.querySelector('.aa-Input');
            if (!input) return;

            // Get the original touch event
            const originalEvent = e.originalEvent;
            if (originalEvent && originalEvent.changedTouches && originalEvent.changedTouches.length > 0) {
                const touch = originalEvent.changedTouches[0];
                
                const newTouchObj = {
                    identifier: touch.identifier,
                    target: input,
                    clientX: touch.clientX,
                    clientY: touch.clientY,
                    screenX: touch.screenX,
                    screenY: touch.screenY,
                    pageX: touch.pageX,
                    pageY: touch.pageY,
                    radiusX: touch.radiusX || 0,
                    radiusY: touch.radiusY || 0,
                    rotationAngle: touch.rotationAngle || 0,
                    force: touch.force || 1
                };

                try {
                    const touchObj = new Touch(newTouchObj);
                    
                    const touchEndEvent = new TouchEvent('touchend', {
                        bubbles: true,
                        cancelable: true,
                        touches: [],
                        targetTouches: [],
                        changedTouches: [touchObj],
                        view: window
                    });
                    
                    input.dispatchEvent(touchEndEvent);
                    
                    // Also focus
                    setTimeout(() => {
                        input.focus();
                        input.click();
                    }, 10);
                } catch (err) {
                    console.log('Touch end redirection failed:', err);
                    input.focus();
                    input.click();
                }
            }
        });

        // Fallback for non-touch devices
        $('.footer-search').on('click', function(e) {
            if (e.originalEvent && e.originalEvent.touches) {
                return; // Already handled by touch
            }
            
            e.preventDefault();
            e.stopPropagation();

            const input = document.querySelector('.aa-Input');
            if (!input) return;

            if (window.algoliaAutocompleteInstance && 
                typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
                window.algoliaAutocompleteInstance.setIsOpen(true);
            }

            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            
            if (window.location.href.indexOf('/customer/') === -1) {
                $('#mobile-account-popup').removeClass('active');
                $('body').css('overflow', '');
            }

            window.scrollTo(0, 0);

            setTimeout(() => {
                input.removeAttribute('readonly');
                input.removeAttribute('disabled');
                input.style.pointerEvents = 'auto';
                input.focus();
                input.click();

                if (input.value) {
                    const length = input.value.length;
                    input.setSelectionRange(length, length);
                }
            }, 50);
        });
    }

    function handleSearchOpen() {
        // This function is no longer used by footer search
        // Keeping it in case it's called elsewhere
        const input = document.querySelector('.aa-Input');
        if (!input) return;

        if (window.algoliaAutocompleteInstance && 
            typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
            window.algoliaAutocompleteInstance.setIsOpen(true);
        }

        $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');

        if (window.location.href.indexOf('/customer/') === -1) {
            $('#mobile-account-popup').removeClass('active');
            $('body').css('overflow', '');
        }

        input.removeAttribute('readonly');
        input.removeAttribute('disabled');
        window.scrollTo(0, 0);
        
        setTimeout(() => {
            input.focus();
            input.click();
            
            if (input.value) {
                const length = input.value.length;
                input.setSelectionRange(length, length);
            }
        }, 50);
    }

    function setupFooterCart() {
        $('.showcart-footer').on('click', function(e) {
            e.preventDefault();

            $('.mobile-menu-close, .mobile-services-close, .close-popup').trigger('click');
            closeAlgolia();
            closeAccountPopup();

            requestAnimationFrame(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' });

                requestAnimationFrame(() => {
                    $('.showcart').trigger('click');
                });
            });
        });
    }

    function setupAccountPopup() {
        $('#open-account-popup, .account-top-link-mobile').on('click', function() {
            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            closeAlgolia();

            requestAnimationFrame(() => {
                $('#mobile-account-popup').addClass('active account');
                $('body').css('overflow', 'hidden');
            });
        });

        $('.account-popup-header .signout, .account-popup-header-in-pages .signout').on('click', function() {
            window.location.href = '/customer/account/logout/';
        });

        $('.account-popup-header-in-pages .close-popup').on('click', function() {
            window.location.href = '/';
        });

        $('.goback').on('click', function() {
            $('#open-account-popup').trigger('click');
        });
    }

    function setupModals() {
        $('[data-modal-trigger]').on('click', function() {
            const modalId = $(this).attr('data-modal-trigger');
            const $modal = $('[data-modal="' + modalId + '"]');

            $('.mobile-menu-close, .minicart-close').trigger('click');
            closeAlgolia();

            requestAnimationFrame(() => {
                $modal.addClass('active');
                $('body').addClass('modal-open');

                requestAnimationFrame(() => {
                    $modal.find('.mobile-modal-container').css('transform', 'translateY(0)');
                });
            });
        });

        $('[data-modal-close]').on('click', function() {
            const modalId = $(this).attr('data-modal-close');
            closeModal(modalId);
        });

        $('.mobile-modal-overlay').on('click', function() {
            const $modal = $(this).closest('.mobile-modal');
            const modalId = $modal.attr('data-modal');
            closeModal(modalId);
        });
    }

    function closeModal(modalId) {
        const $modal = $('[data-modal="' + modalId + '"]');

        requestAnimationFrame(() => {
            $modal.find('.mobile-modal-container').css('transform', 'translateY(100%)');

            setTimeout(() => {
                $modal.removeClass('active');
                $('body').removeClass('modal-open');
            }, 300);
        });
    }

    function handleAutoLogin() {
        if (!window.matchMedia("(max-width: 768px)").matches) return;

        const path = window.location.pathname;
        const isAccountPage = path === '/customer/account' ||
            path === '/customer/account/' ||
            path === '/customer/account/index' ||
            path === '/customer/account/index/';

        if (isAccountPage && window.showLoginOverlay) {
            setTimeout(() => {
                $('#open-account-popup').trigger('click');
                window.showLoginOverlay = false;
            }, 100);
        }
    }

    function handleEmailChatLink() {
        const params = new URLSearchParams(window.location.search);
        if (params.get('openChat') === '1') {
            console.log('open chat from email link');
            if (typeof zE !== 'undefined') {
                zE('messenger', 'open');
            }
        }
    }

    function closeAlgolia() {
        if (window.algoliaAutocompleteInstance &&
            typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {

            window.algoliaAutocompleteInstance.setIsOpen(false);

            const input = document.querySelector('input[type="search"], .aa-Input');
            if (input) {
                input.blur();

                // Mobile keyboard dismissal
                if (/iPhone|iPad|iPod|Android/.test(navigator.userAgent)) {
                    input.setAttribute('readonly', 'readonly');
                    setTimeout(() => {
                        input.removeAttribute('readonly');
                        input.blur();
                    }, 100);
                }
            }
        }
    }

    function closeAccountPopup() {
        if (window.location.href.indexOf('/customer/') !== -1) {
            window.location.href = '/';
        } else {
            requestAnimationFrame(() => {
                $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
                closeAlgolia();
                $('#mobile-account-popup').removeClass('active');
                $('body').css('overflow', '');
            });
        }
    }
});