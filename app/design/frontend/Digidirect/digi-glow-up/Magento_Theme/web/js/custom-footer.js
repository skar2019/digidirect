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

        // ✅ AGGRESSIVE FIX: Clear ALL active states, set the correct one, then enforce continuously
        $footerNavItems.removeClass('active');
        initializeActiveStates();
        
        // Start continuous enforcer
        startActiveStateEnforcer();

        // Event delegation for better performance
        setupEventDelegation();

        // Individual handlers
        setupFooterMenuIcon();
        setupFooterSearchWithInput();
        setupFooterCart();
        setupAccountPopup();
        setupModals();

        // Auto-open login overlay on account pages
        handleAutoLogin();

        // Chat from email link
        handleEmailChatLink();
    });

    // ✅ ENFORCER: Runs every 50ms to guarantee only one active button at a time.
    // This is the nuclear option — catches all edge cases including:
    // - Clicks during page load
    // - Race conditions between handlers
    // - Residual state from previous page
    // - Multiple handlers firing simultaneously
    function startActiveStateEnforcer() {
        setInterval(function() {
            const activeItems = document.querySelectorAll('.footer-nav-item.active');
            
            if (activeItems.length > 1) {
                console.warn('⚠️ Multiple active buttons detected, fixing...', activeItems);
                
                // Keep only the LAST one that became active
                for (let i = 0; i < activeItems.length - 1; i++) {
                    activeItems[i].classList.remove('active');
                }
            }
        }, 50); // Check every 50ms
    }

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
            return;
        }

        if (
            path === '/customer/account' ||
            path === '/customer/account/' ||
            path === '/customer/account/index' ||
            path === '/customer/account/index/'
        ) {
            document.querySelector('.account-footer-menu')?.classList.add('active');
            return;
        }
    }

    function setupEventDelegation() {
        const $footerNavItems = $('.footer-nav-item');
        const $closeButtons = $('.mobile-menu-close, .mobile-services-close, .minicart-close, .close-popup');

        // Close button handler — clear all active states
        $closeButtons.on('click', function() {
            requestAnimationFrame(() => {
                $footerNavItems.removeClass('active');
            });
        });

        // Footer nav item handler — set exactly one active at a time
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

    /**
     * Handles the footer search input to trigger keyboard on mobile
     */
    function setupFooterSearchWithInput() {
        const $footerSearchInput = $('.footer-search-input');
        
        if (!$footerSearchInput.length) {
            console.warn('Footer search input not found');
            return;
        }

        console.log('✅ Footer search input initialized');

        $footerSearchInput.on('focus', function() {
            console.log('✅ Footer search input focused!');
            
            const $input = $(this);
            
            $input.removeAttr('readonly');
            
            $('.mobile-menu-close, .mobile-services-close, .minicart-close').trigger('click');
            
            if (window.location.href.indexOf('/customer/') === -1) {
                $('#mobile-account-popup').removeClass('active');
                $('body').css('overflow', '');
            }
            
            if (window.algoliaAutocompleteInstance && 
                typeof window.algoliaAutocompleteInstance.setIsOpen === 'function') {
                window.algoliaAutocompleteInstance.setIsOpen(true);
            }
            
            window.scrollTo(0, 0);
            
            setTimeout(() => {
                const algoliaInput = document.querySelector('.aa-Input');
                if (algoliaInput) {
                    console.log('✅ Transferring focus to Algolia input');
                    
                    algoliaInput.removeAttribute('readonly');
                    algoliaInput.removeAttribute('disabled');
                    algoliaInput.focus();
                    
                    $input.blur();
                    $input.attr('readonly', 'readonly');
                    
                    console.log('✅ Focus transferred successfully');
                } else {
                    console.warn('❌ Algolia input not found');
                }
            }, 150);
        });
        
        $footerSearchInput.on('input', function() {
            $(this).val('');
        });
        
        $('.footer-search').on('click', function(e) {
            e.preventDefault();
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