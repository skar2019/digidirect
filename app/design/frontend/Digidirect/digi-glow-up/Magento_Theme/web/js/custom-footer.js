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
        setupFooterSearchWithInput(); // NEW FUNCTION
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

        // ✅ FIX: Track the pending rAF so we can cancel it before scheduling a new one.
        // Without this, rapid clicks queue multiple frames; the removeClass/addClass calls
        // from earlier frames can fire AFTER a later frame's addClass, leaving items
        // stuck in the active state simultaneously.
        let pendingRaf = null;

        function setActiveItem($target) {
            if (pendingRaf) {
                cancelAnimationFrame(pendingRaf);
            }
            pendingRaf = requestAnimationFrame(() => {
                $footerNavItems.removeClass('active');
                if ($target) {
                    $target.addClass('active');
                }
                pendingRaf = null;
            });
        }

        // Close button handler — clear active state
        $closeButtons.on('click', function() {
            setActiveItem(null);
        });

        // Footer nav item handler — set exactly one item active
        $footerNavItems.on('click', function() {
            setActiveItem($(this));
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

    /**
     * NEW FOOTER SEARCH WITH INPUT
     * Handles the footer search input to trigger keyboard on mobile
     */
    function setupFooterSearchWithInput() {
        const $footerSearchInput = $('.footer-search-input');
        
        if (!$footerSearchInput.length) {
            console.warn('Footer search input not found');
            return;
        }

        console.log('✅ Footer search input initialized');

        // When the input gets focus (user taps the button area)
        $footerSearchInput.on('focus', function() {
            console.log('✅ Footer search input focused!');
            
            const $input = $(this);
            
            // Remove readonly so keyboard appears
            $input.removeAttr('readonly');
            
            // Close other menus
            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            
            if (window.location.href.indexOf('/customer/') === -1) {
                $('#mobile-account-popup').removeClass('active');
                $('body').css('overflow', '');
            }
            
            // Open Algolia autocomplete
            if (window.algoliaAutocompleteInstance && 
                typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
                window.algoliaAutocompleteInstance.setIsOpen(true);
            }
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Wait for Algolia to render, then transfer focus
            setTimeout(() => {
                const algoliaInput = document.querySelector('.aa-Input');
                if (algoliaInput) {
                    console.log('✅ Transferring focus to Algolia input');
                    
                    algoliaInput.removeAttribute('readonly');
                    algoliaInput.removeAttribute('disabled');
                    algoliaInput.focus();
                    
                    // Blur our fake input and make it readonly again
                    $input.blur();
                    $input.attr('readonly', 'readonly');
                    
                    console.log('✅ Focus transferred successfully');
                } else {
                    console.warn('❌ Algolia input not found');
                }
            }, 150);
        });
        
        // Prevent typing into the fake input
        $footerSearchInput.on('input', function() {
            $(this).val('');
        });
        
        // Prevent default button click behavior
        $('.footer-search').on('click', function(e) {
            e.preventDefault();
            // Let the input's focus event handle everything
        });
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