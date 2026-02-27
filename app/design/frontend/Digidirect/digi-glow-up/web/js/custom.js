define([
    "jquery",
    "ko",
    "uiRegistry",
    "Magento_Ui/js/core/app",
    "Magento_Customer/js/customer-data",
    "domReady!",
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, ko, registry, uiApp, customerData) {
    "use strict";

    $(function () {
        /* ========================
   ✅ Sticky Header (Self-correcting)
======================== */
        const $header = $(".header.content");
        let $placeholder = $(".header-placeholder");

        if (!$placeholder.length) {
            $placeholder = $('<div class="header-placeholder"></div>');
            $header.after($placeholder);
        }

        let stickyPoint = 0;
        let isSticky = false;
        let lastTop = 0;
        let stableCounter = 0;

        function recalcStickyPoint() {
            if (!isSticky && $header.length) {
                stickyPoint = $header.offset().top;
            }
        }

        function setSticky(active) {
            if (active && !isSticky) {
                $placeholder.height($header.outerHeight()).show();
                $header.addClass("is-sticky");
                isSticky = true;
            } else if (!active && isSticky) {
                $header.removeClass("is-sticky");
                isSticky = false;
                $placeholder.hide();
            }
        }

        function updateSticky() {
            const scrollTop = $(window).scrollTop();
            setSticky(scrollTop >= stickyPoint);
        }

        /* 🧠 Continuous layout stabilization check */
        function watchLayoutStability() {
            const currentTop = $header.offset().top;
            if (currentTop === lastTop) {
                stableCounter++;
            } else {
                stableCounter = 0;
                lastTop = currentTop;
            }

            if (stableCounter < 10) {
                // not stable yet → keep checking every frame
                requestAnimationFrame(watchLayoutStability);
            } else {
                // stable → recalc sticky safely
                recalcStickyPoint();
                updateSticky();
            }
        }

        $(window).on("load", () => {
            // wait for scroll restore + first layout
            setTimeout(() => {
                requestAnimationFrame(() => {
                    recalcStickyPoint();
                    updateSticky();
                    watchLayoutStability();
                });
            }, 400);
        });

        $(window).on("scroll resize", () => {
            recalcStickyPoint();
            updateSticky();
        });

        $(".ul.ruby-menu li.ruby-menu-mega-blog").click(function (e) {
            $("body").removeClass("blur-active");
            $(".aa-Panel.is-ready").removeClass("is-ready");
        });

        /* ========================
       🧊 Global Blur Overlay
    ======================== */
        const $blurOverlay = $('<div class="global-blur-overlay"></div>');
        if (!$(".global-blur-overlay").length) $("body").append($blurOverlay);

        function positionBlurOverlay() {
            const $overlay = $(".global-blur-overlay");
            const $main = $("#maincontent");
            if (!$main.length) return;

            const mainOffset = $main.offset().top;
            const documentHeight = Math.max(
                $(document).height(),
                $("body").prop("scrollHeight")
            );
            const height = documentHeight - mainOffset;

            if ($("body").hasClass("blur-active")) {
                $overlay.css({
                    position: "absolute",
                    top: mainOffset + "px",
                    left: 0,
                    width: "100%",
                    height: height + "px",
                });
            } else {
                $overlay.css({ height: "0" });
            }
        }

        positionBlurOverlay();
        $(window).on("resize scroll", positionBlurOverlay);

        if (window.MutationObserver) {
            const blurObserver = new MutationObserver(() =>
                positionBlurOverlay()
            );
            blurObserver.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }

        const blurStyle = `
      .global-blur-overlay {
        width: 100%;
        left: 0;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        opacity: 0;
        transition: opacity 0.3s ease, height 0.3s ease;
        pointer-events: none;
        z-index: 9;
      }
      body.blur-active .global-blur-overlay {
        opacity: 1;
      }
    `;
        $("head").append(`<style>${blurStyle}</style>`);

        $(document).on(
            "mouseenter click",
            ".ruby-menu-mega-blog:not(.just-link)",
            function () {
                const $panel = $(".aa-Panel");
                const $input = $("#autocomplete-0-input");

                // Hide aa-Panel
                if ($panel.length) {
                    $panel.css({ visibility: "hidden", opacity: 0 });
                    setTimeout(() => $panel.remove(), 100);
                }
                if ($input.length) $input.trigger("blur");

                // ✅ Apply blur
                $("body").addClass("blur-active");
                positionBlurOverlay();
            }
        );

        $(document).on(
            "mouseleave",
            ".ruby-menu-mega-blog:not(.just-link)",
            function () {
                $("body").removeClass("blur-active");
                positionBlurOverlay();
            }
        );

        // Blur Active AA Panel Only When Not Mobile
        let aaPanelObserver;

        function initBlurObserver() {
            const $body = $("body");

            // 🧹 Reset any inline styles
            $body.css({ position: "", top: "", width: "", overflowY: "" });

            const hasAaPanel = $(".aa-Panel").length > 0;

            // --- Set up MutationObserver if not already ---
            if (window.MutationObserver && !aaPanelObserver) {
                aaPanelObserver = new MutationObserver(() => {
                    if ($(".aa-Panel").length) {
                        $body.addClass("blur-active");
                    } else {
                        $body.removeClass("blur-active");
                    }
                    positionBlurOverlay();
                });

                aaPanelObserver.observe(document.body, {
                    childList: true,
                    subtree: true,
                });
            }

            // --- Apply lock CSS if aa-Panel exists ---
            if (hasAaPanel) {
                $body.css({
                    position: "fixed",
                    width: "100%",
                });
            } else {
                $body.css({ position: "", width: "" });
            }
        }

        // Initialize and re-check on resize
        $(window).on("resize", initBlurObserver);
        initBlurObserver();

        /* ========================
        ✨ Sync #pa-welcome-back with Body Blur
            Show only to returning visitors, once every 6 hours
            ======================== */
        const $container = $("#welcome-back-widget-desktop");
        const $target = $("#pa-welcome-back");

        // 6 hours in milliseconds
        const SIX_HOURS = 6 * 60 * 60 * 1000;
        const now = Date.now();
        const lastShown = localStorage.getItem("welcomeBackLastShown");

        // Check if first-time visitor
        const isFirstVisit = !localStorage.getItem("hasVisited");

        const canShow =
            !isFirstVisit &&
            (!lastShown || now - parseInt(lastShown, 10) > SIX_HOURS);

        // Mark visitor as having visited (for future visits)
        localStorage.setItem("hasVisited", "true");

        function isNewsPopupInDOM() {
            return !!document.getElementById("newspopup_up_bg_13");
        }

        function hideWelcomeBack() {
            $target.removeClass("active");
            $("body").removeClass("pa-welcome-active");
            if (
                !$(".aa-Panel").length &&
                !$(".ruby-menu-mega-blog:hover").length
            ) {
                $("body").removeClass("blur-active");
            }
        }

        // Watch for news popup being injected into the DOM at any point
        const newsPopupObserver = new MutationObserver(() => {
            const newsPopup = document.getElementById("newspopup_up_bg_13");
            if (newsPopup) {
                // Hide it immediately on injection
                newsPopup.style.visibility = "hidden";
                setTimeout(() => {
                    hideWelcomeBack();
                    newsPopup.style.visibility = "visible";
                }, 3000);
                newsPopupObserver.disconnect();
            }
        });
        newsPopupObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });

        if ($container.length && $target.length && canShow) {
            // Wait 1s then do a final DOM check before showing
            setTimeout(() => {
                if (isNewsPopupInDOM()) return;

                $target.addClass("active");
                localStorage.setItem("welcomeBackLastShown", Date.now());

                // Keep polling for 5 seconds after showing, in case popup appears late
                const pollInterval = setInterval(() => {
                    if (isNewsPopupInDOM()) {
                        hideWelcomeBack();
                        clearInterval(pollInterval);
                    }
                }, 200);
                setTimeout(() => clearInterval(pollInterval), 5000);
            }, 1000);
        }

        // Observe dynamic class changes on #pa-welcome-back
        const target = document.querySelector("#pa-welcome-back");
        if (target) {
            new MutationObserver(toggleOverlay).observe(target, {
                attributes: true,
                attributeFilter: ["class"],
            });
        }

        function toggleWelcomeBackBlur() {
            const $target = $("#pa-welcome-back");
            const isActive = $target.hasClass("active");
            if (isActive) {
                $("body").addClass("blur-active pa-welcome-active");
            } else {
                $("body").removeClass("pa-welcome-active");
                // Only remove blur if no other blur source is active
                if (
                    !$(".aa-Panel").length &&
                    !$(".ruby-menu-mega-blog:hover").length &&
                    !$("#pa-welcome-back.active").length
                ) {
                    $("body").removeClass("blur-active");
                }
            }
            if (typeof positionBlurOverlay === "function")
                positionBlurOverlay();
        }


        // Observe #pa-welcome-back for .active changes
        //    if (window.MutationObserver) {
        //      const paEl = document.getElementById('pa-welcome-back')
        //      if (paEl) {
        //        const observer = new MutationObserver(toggleWelcomeBackBlur)
        //        observer.observe(paEl, { attributes: true, attributeFilter: ['class'] })
        //      }
        //    }

        // Run once at load (in case it's already active)
        //toggleWelcomeBackBlur()

        /* ========================
            🌐 Reposition #pa-welcome-back & #pa-upsell
       ======================== */
        const paWidgets = ["#pa-welcome-back", "#pa-upsell"];

        paWidgets.forEach((selector) => {
            const $widget = $(selector);
            if ($widget.length && !$widget.parent().hasClass("page-wrapper")) {
                $(".page-wrapper").before($widget);
            }
        });

        /* ========================
        ❌ Close Welcome Back & Remove Blur
     ======================== */
        $(document).on(
            "click",
            "#welcome-back-close, #maincontent, #welcome-back-widget-desktop .action.tocart.primary",
            function () {
                $("#pa-welcome-back").removeClass("active");
                $("body").removeClass("blur-active");
                positionBlurOverlay(); // keep your old blur overlay working
            }
        );

        /* ========================
   🛒 AJAX Minicart - CLEAN (counter-based)
        ✅ Excludes account pages
     ======================== */

        const $minicart = $('[data-block="minicart"]');
        let scrollY = 0;
        let isLocked = false;

        function isMobile() {
            return window.innerWidth <= 768;
        }

        function isAccountPage() {
            const path = window.location.pathname;
            return /\/customer|\/account|\/login|\/register|\/forgotpassword/i.test(
                path
            );
        }

        function lockScroll() {
            if (!isMobile() || isLocked) return;
            scrollY = window.scrollY;
            document.body.dataset.scrollY = scrollY;
            isLocked = true;

            setTimeout(() => {
                [document.documentElement, document.body].forEach((el) => {
                    el.style.position = "fixed";
                    el.style.top = `-${scrollY}px`;
                    el.style.left = "0";
                    el.style.right = "0";
                    el.style.width = "100%";
                    el.style.overflow = "hidden";
                });
            }, 150);
        }

        function unlockScroll() {
            if (!isLocked) return;
            const savedScrollY = parseInt(
                document.body.dataset.scrollY || "0",
                10
            );
            isLocked = false;
            [document.documentElement, document.body].forEach((el) => {
                el.style.position = "";
                el.style.top = "";
                el.style.left = "";
                el.style.right = "";
                el.style.width = "";
                el.style.overflow = "";
            });

            delete document.body.dataset.scrollY;
            setTimeout(() => window.scrollTo(0, savedScrollY), 100);
        }

        function updateMinicartOverlay() {
            const $minicartDropdown = $(
                '.block-minicart[data-role="dropdownDialog"]'
            );
            const $headerMenu = $(".ruby-menu-demo-header");
            const $miniOverlay = $(".minicart-overlay");

            const isVisible =
                $minicartDropdown.length &&
                $minicartDropdown.is(":visible") &&
                $minicartDropdown.css("display") !== "none";

            if (isVisible) {
                if ($headerMenu.length) $headerMenu.css("z-index", 0);
                if ($miniOverlay.length) $miniOverlay.css("display", "block");
                lockScroll();
            } else {
                if ($headerMenu.length) $headerMenu.css("z-index", "");
                if ($miniOverlay.length) $miniOverlay.css("display", "none");
                setTimeout(unlockScroll, 300);
            }
        }

        function openMinicart() {
            const $showCart = $minicart.find(".action.showcart");
            if ($showCart.length) $showCart.trigger("click");
            else $minicart.trigger("click");

            if (isMobile()) {
                setTimeout(
                    () => window.scrollTo({ top: 0, behavior: "smooth" }),
                    200
                );
            }

            setTimeout(updateMinicartOverlay, 300);
        }

        // Keep overlay sync
        if (window.MutationObserver) {
            const miniObserver = new MutationObserver(() =>
                updateMinicartOverlay()
            );
            miniObserver.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ["style", "class"],
            });
        }

        const miniInterval = setInterval(updateMinicartOverlay, 400);
        $(window).on("unload beforeunload", () => clearInterval(miniInterval));
        $(document).on("click", ".minicart-close", () =>
            setTimeout(unlockScroll, 300)
        );

        /* ========================
        🧩 Persistent Auto Minicart (Counter-based)
        ✅ Excludes first load, account pages & cart page
     ======================== */
        function setupPersistentAutoMinicart() {
            if (
                isAccountPage() ||
                window.location.pathname.includes("/checkout/cart")
            ) return;

            let lastCartCount = -1; // -1 = baseline not yet set
            let baselineSet = false;
            let upsellReady = false;
            let minicartQueued = false;

            function attachObserver($counter) {
                if ($counter.data("observer-attached")) return;
                $counter.data("observer-attached", true);

                const observer = new MutationObserver(() => {
                    const currentCount = parseInt($counter.text() || 0);

                    // ✅ First time counter renders with a real value = set baseline, never open
                    if (!baselineSet) {
                        // Wait until Knockout renders a stable non-empty value
                        if ($counter.text().trim() === '') return;
                        lastCartCount = currentCount;
                        baselineSet = true;
                        console.log("🛒 Baseline cart count set:", lastCartCount);
                        return;
                    }

                    // ✅ Only open if count genuinely increased AFTER baseline was set
                    if (currentCount > lastCartCount) {
                        const $upsell = $("#pa-upsell");
                        const isUpsellActive = $upsell.length && $upsell.hasClass("active");

                        if (isUpsellActive) {
                            console.log("🟡 Upsell active — delaying minicart open.");
                            minicartQueued = true;
                            lastCartCount = currentCount;
                            return;
                        }

                        const $minicartDropdown = $('.block-minicart[data-role="dropdownDialog"]');
                        if (!$minicartDropdown.is(":visible")) openMinicart();
                    }

                    lastCartCount = currentCount;
                });

                observer.observe($counter[0], {
                    childList: true,
                    subtree: true,
                    characterData: true,
                });
            }

            // Watch for counter elements injected by Knockout
            const bodyObserver = new MutationObserver(() => {
                $('.counter-number[data-bind*="summary_count"]').each(function () {
                    attachObserver($(this));
                });

                if (!upsellReady && $("#pa-upsell").length) {
                    upsellReady = true;
                    observeUpsell();
                }
            });

            bodyObserver.observe(document.body, {
                childList: true,
                subtree: true,
            });

            // Also attach to any already-existing counters
            $('.counter-number[data-bind*="summary_count"]').each(function () {
                attachObserver($(this));
            });

            function observeUpsell() {
                const upsellEl = document.getElementById("pa-upsell");
                if (!upsellEl) return;

                const upsellObserver = new MutationObserver(() => {
                    const isActive = $("#pa-upsell").hasClass("active");
                    if (!isActive && minicartQueued) {
                        console.log("🟢 Upsell closed — opening minicart now.");
                        minicartQueued = false;
                        openMinicart();
                    }
                });

                upsellObserver.observe(upsellEl, {
                    attributes: true,
                    attributeFilter: ["class"],
                });
            }
        }

        $(document).ready(() => setupPersistentAutoMinicart());

        //Overlay Observer
        function toggleOverlay() {
            const hasActivePopup =
                $("#pa-welcome-back").hasClass("active") ||
                $("#pa-upsell").hasClass("active");

            if (hasActivePopup) {
                $(".page-wrapper").addClass("has-overlay");
                $("body").addClass("overlay-active");
            } else {
                $(".page-wrapper").removeClass("has-overlay");
                $("body").removeClass("overlay-active");
            }
        }

        // 🧩 Run once on page load
        toggleOverlay();

        // 🧠 Watch dynamically for #pa-upsell or #pa-welcome-back changes
        if (window.MutationObserver) {
            const observer = new MutationObserver(() => {
                // Run toggle check on any class change or new element
                toggleOverlay();
            });

            // Observe the entire document for dynamic popups
            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ["class"],
            });
        }

        /* ========================
            🌀 Owl Carousel 2-Finger Swipe (Smooth Apple-like)
            ✅ Works together with Owl's 1-Finger native swipe
         ======================== */
         function initTwoFingerSwipe($scope = $(document)) {
             $scope.find(".owl-carousel").each(function () {
                 const $carousel = $(this);
                 if ($carousel.data("twoFingerBound")) return;
                 $carousel.data("twoFingerBound", true);

                 let startX = 0;
                 let isTwoFinger = false;
                 let hasSwiped = false;
                 let isAtEdge = false;
                 const threshold = 50;
                 const lockDuration = 250;
                 const transitionSpeed = 600;
                 const edgeElastic = 40;
                 const node = $carousel[0];

                 node.addEventListener(
                     "touchstart",
                     function (e) {
                         const touches = e.touches;
                         if (touches.length === 2) {
                             isTwoFinger = true;
                             startX =
                                 (touches[0].clientX + touches[1].clientX) / 2;
                             hasSwiped = false;
                             isAtEdge = false;
                             e.stopImmediatePropagation();
                         } else {
                             isTwoFinger = false;
                         }
                     },
                     { capture: true }
                 );

                 node.addEventListener(
                     "touchmove",
                     function (e) {
                         if (!isTwoFinger || hasSwiped) return;
                         const touches = e.touches;
                         if (touches.length !== 2) return;

                         const currentX =
                             (touches[0].clientX + touches[1].clientX) / 2;
                         const deltaX = currentX - startX;

                         const carouselData = $carousel.data("owl.carousel");
                         if (!carouselData) return;

                         const atFirst = carouselData.current() === 0;
                         const atLast =
                             carouselData.current() === carouselData.maximum();

                         if ((atFirst && deltaX > 0) || (atLast && deltaX < 0)) {
                             const elastic = Math.min(
                                 Math.abs(deltaX) / 4,
                                 edgeElastic
                             );
                             $carousel.css(
                                 "transform",
                                 `translateX(${
                                     deltaX > 0 ? elastic : -elastic
                                 }px)`
                             );
                             isAtEdge = true;
                             return;
                         }

                         if (Math.abs(deltaX) > threshold) {
                             if (deltaX > 0) {
                                 $carousel.trigger("prev.owl.carousel", [
                                     transitionSpeed,
                                 ]);
                             } else {
                                 $carousel.trigger("next.owl.carousel", [
                                     transitionSpeed,
                                 ]);
                             }

                             hasSwiped = true;
                             e.preventDefault();
                             e.stopImmediatePropagation();

                             setTimeout(() => {
                                 hasSwiped = false;
                                 isTwoFinger = false;
                             }, lockDuration);
                         }
                     },
                     { capture: true }
                 );

                 node.addEventListener(
                     "touchend",
                     function () {
                         if (isAtEdge) {
                             $carousel.css({
                                 transition:
                                     "transform 0.3s cubic-bezier(0.25, 1, 0.5, 1)",
                                 transform: "translateX(0)",
                             });
                             setTimeout(
                                 () => $carousel.css("transition", ""),
                                 300
                             );
                             isAtEdge = false;
                         }
                         isTwoFinger = false;
                     },
                     { capture: true }
                 );

                 node.addEventListener(
                     "touchcancel",
                     function () {
                         isTwoFinger = false;
                         if (isAtEdge) {
                             $carousel.css({
                                 transition:
                                     "transform 0.3s cubic-bezier(0.25, 1, 0.5, 1)",
                                 transform: "translateX(0)",
                             });
                             setTimeout(
                                 () => $carousel.css("transition", ""),
                                 300
                             );
                             isAtEdge = false;
                         }
                     },
                     { capture: true }
                 );

                 // ✅ FIXED: Exact same logic as Slick slider
                 $carousel.on("wheel", function (e) {
                     const event = e.originalEvent;

                     // Detect horizontal gesture with minimum threshold
                     if (Math.abs(event.deltaX) > Math.abs(event.deltaY) && Math.abs(event.deltaX) > 3) {
                         e.preventDefault();

                         // Initialize accumulator
                         if (!$carousel.data('deltaAccumulator')) {
                             $carousel.data('deltaAccumulator', 0);
                         }

                         // Accumulate delta
                         let accumulated = $carousel.data('deltaAccumulator') + event.deltaX;
                         $carousel.data('deltaAccumulator', accumulated);

                         // Trigger when threshold reached
                         const wheelThreshold = 30;
                         if (!$carousel.data('swiping') && Math.abs(accumulated) > wheelThreshold) {
                             $carousel.data('swiping', true);

                             if (accumulated > 0) {
                                 $carousel.trigger("next.owl.carousel", [transitionSpeed]);
                             } else {
                                 $carousel.trigger("prev.owl.carousel", [transitionSpeed]);
                             }

                             $carousel.data('deltaAccumulator', 0); // Reset accumulator
                         }

                         // Reset after gesture ends
                         clearTimeout($carousel.data('swipeTimeout'));
                         const timeout = setTimeout(() => {
                             $carousel.data('swiping', false);
                             $carousel.data('deltaAccumulator', 0);
                         }, 100);
                         $carousel.data('swipeTimeout', timeout);
                     }
                 });
             });
         }

        /* ================================
   🧠 Observe only the minicart dialog
================================ */
        $(document).ready(function () {
            initTwoFingerSwipe();

            const dialogs = document.querySelectorAll(".mage-dropdown-dialog");
            dialogs.forEach((dialog) => {
                // Only attach observer to the one containing the minicart
                if (!dialog.querySelector(".block-minicart")) return;

                const observer = new MutationObserver(() => {
                    const isVisible = $(dialog).css("display") !== "none";
                    if (isVisible) {
                        console.log(
                            "🛒 Minicart opened — binding 2-finger swipe"
                        );
                        setTimeout(() => {
                            const $carousels = $(dialog).find(".owl-carousel");
                            $carousels.each(function () {
                                const $this = $(this);
                                const checkOwl = setInterval(() => {
                                    if ($this.data("owl.carousel")) {
                                        clearInterval(checkOwl);
                                        initTwoFingerSwipe($this.parent());
                                    }
                                }, 200);
                                setTimeout(() => clearInterval(checkOwl), 3000);
                            });
                        }, 400);
                    }
                });

                observer.observe(dialog, {
                    attributes: true,
                    attributeFilter: ["style"],
                });
            });
        });

        /* ========================
       🔍 Update Autocomplete Header
    ======================== */
        $(document).on("keyup", "#autocomplete-0-input", function () {
            const query = $(this).val().trim();
            setTimeout(function () {
                const $headerEl = $(
                    '.aa-Source[data-autocomplete-source-id="products"] .aa-SourceHeader p'
                );
                if ($headerEl.length) {
                    $headerEl.text(
                        query.length
                            ? `Results for "${query}"`
                            : "Top Selling Products"
                    );
                }
            }, 100);
        });

        /* ========================
       🛒 Minicart Modal Add Class
    ======================== */
        const observer2 = new MutationObserver(function () {
            const $minicartModal = $(
                "aside.modal-popup .modal-content #minicart-content-wrapper"
            ).closest("aside.modal-popup");
            if (
                $minicartModal.length &&
                !$minicartModal.hasClass("minicart-modal")
            ) {
                $minicartModal.addClass("minicart-modal");
            }
        });
        observer2.observe(document.body, { childList: true, subtree: true });

        /* ========================
       👁️ Hide Facelift Dropdown when AA Panel active
    ======================== */
        const $dropdown = $(".facelift-dropdown-container");
        if ($dropdown.length) {
            function toggleDropdown() {
                if ($(".aa-Panel").length) $dropdown.hide();
                else $dropdown.show();
            }
            toggleDropdown();
            setInterval(toggleDropdown, 300);
        }

        /* ========================
       ✅ Rheostat Tooltip Boundary Fix
    ======================== */
        function limitRheostatTooltips() {
            function init() {
                const $slider = $(".ais-RangeSlider");
                if (!$slider.length) {
                    setTimeout(init, 500);
                    return;
                }

                const $handles = $slider.find(".rheostat-handle");

                function adjustTooltips() {
                    const sliderRect = $slider[0].getBoundingClientRect();
                    $handles.each(function () {
                        const $handle = $(this);
                        const $tooltip = $handle.find(".rheostat-tooltip");
                        if ($tooltip.length) {
                            const handleRect =
                                $handle[0].getBoundingClientRect();
                            const tooltipRect =
                                $tooltip[0].getBoundingClientRect();
                            const tooltipLeft =
                                handleRect.left +
                                handleRect.width / 2 -
                                tooltipRect.width / 2;
                            const minLeft = sliderRect.left;
                            const maxLeft =
                                sliderRect.right - tooltipRect.width;
                            let newLeft = tooltipLeft;
                            if (tooltipLeft < minLeft) newLeft = minLeft;
                            if (tooltipLeft > maxLeft) newLeft = maxLeft;
                            const offsetLeft = newLeft - handleRect.left;
                            $tooltip.css({
                                position: "absolute",
                                left: offsetLeft + "px",
                                transform: "translateX(0)",
                            });
                        }
                    });
                }

                adjustTooltips();
                $(window).on("resize", adjustTooltips);
                const observer = new MutationObserver(adjustTooltips);
                $handles.each(function () {
                    observer.observe(this, {
                        attributes: true,
                        attributeFilter: ["style"],
                    });
                });
                $(document).on(
                    "pointermove mousemove touchmove",
                    adjustTooltips
                );
            }
            init();
        }
        limitRheostatTooltips();

        /* ========================
       === RIBBON LOGIC
    ======================== */
        function toggleRibbonVisibility() {
            const $ribbons = $(".aa-Item .ribbon-digideals");
            let $input = $("#autocomplete-0-input");
            if (!$input.length) $input = $(".aa-Panel").find("input").first();
            let val = "";
            if ($input && $input.length) {
                val = String($input.val() || "").trim();
            }
            $ribbons.css("visibility", val === "" ? "hidden" : "visible");
        }

        setTimeout(toggleRibbonVisibility, 150);
        $(document).on(
            "input",
            "#autocomplete-0-input",
            toggleRibbonVisibility
        );
        $(document).on("input", ".aa-Panel input", toggleRibbonVisibility);
        const ribbonObserver = new MutationObserver(() =>
            toggleRibbonVisibility()
        );
        ribbonObserver.observe(document.body, {
            childList: true,
            subtree: true,
        });
        const checkInterval = setInterval(toggleRibbonVisibility, 500);
        setTimeout(() => clearInterval(checkInterval), 15000);

        /* ========================
   📱 Mobile Menu Overlay Toggle
======================== */
        const $mobileMenu = $(".mobile-menu");
        const $mobileMenuToggle = $(".mobile-menu-icon");
        const $mobileFooterMenuToggle = $(
            ".footer-mobile-menu-icon"
        ); /* clint */

        if ($mobileMenu.length && $mobileMenuToggle.length) {
            // Ensure initial state hidden
            $mobileMenu.removeClass("active");
            $("body").removeClass("menu-open");

            $mobileMenuToggle.on("click", function (e) {
                e.preventDefault();
                const isActive = $mobileMenu
                    .toggleClass("active")
                    .hasClass("active");
                $("body").toggleClass("menu-open", isActive);
                $mobileMenuToggle.attr("aria-expanded", isActive);
            });

            // Optional: close when clicking outside or pressing ESC
            $(document).on("click", function (e) {
                if (
                    $mobileMenu.hasClass("active") &&
                    !$(e.target).closest(
                        ".mobile-menu, .mobile-menu-icon, .footer-mobile-menu-icon"
                    ).length
                ) {
                    $mobileMenu.removeClass("active");
                    $("body").removeClass("menu-open");
                    $mobileMenuToggle.attr("aria-expanded", false);
                }
            });

            $(document).on("keydown", function (e) {
                if (e.key === "Escape" && $mobileMenu.hasClass("active")) {
                    $mobileMenu.removeClass("active");
                    $("body").removeClass("menu-open");
                    $mobileMenuToggle.attr("aria-expanded", false);
                }
            });
        }

        /* ========================
   📱 Apple-style Multi-Level Navigation
======================== */

        const $menuContainer = $(".mobile-menu-content");
        const $menuLevels = $menuContainer.find(".menu-level");
        const $backBtn = $(".back-btn");
        const $menuTitle = $(".mobile-menu-title");

        let menuHistory = [];

        // Handle click to go to next level
        $(document).on("click", ".menu-item[data-target]", function () {
            const target = $(this).data("target");
            const $currentLevel = $menuLevels.filter(".active");
            const $nextLevel = $menuLevels.filter(`[data-parent="${target}"]`);

            if ($nextLevel.length) {
                menuHistory.push($currentLevel);
                $currentLevel.removeClass("active").addClass("previous");
                $nextLevel.addClass("active");

                $menuTitle.text($(this).text());
                $backBtn.show();
            }
        });

        // Back button handler
        $backBtn.on("click", function () {
            const $currentLevel = $menuLevels.filter(".active");
            const $prevLevel = menuHistory.pop();

            if ($prevLevel && $prevLevel.length) {
                $currentLevel.removeClass("active");
                $prevLevel.removeClass("previous").addClass("active");

                if (menuHistory.length === 0) {
                    $menuTitle.text("Menu");
                    $backBtn.hide();
                } else {
                    const parentTarget = $prevLevel.data("parent") || "Menu";
                    $menuTitle.text(
                        parentTarget.charAt(0).toUpperCase() +
                        parentTarget.slice(1)
                    );
                }
            }
        });

        // Reset to root when menu closes
        $(document).on("click", ".mobile-menu-close", function () {
            resetMenuToRoot();
        });

        function resetMenuToRoot() {
            $menuLevels.removeClass("active previous");
            $menuLevels.filter('[data-level="1"]').addClass("active");
            $menuTitle.text("Menu");
            $backBtn.hide();
            menuHistory = [];
        }

        const $mobileMenuClose = $(".mobile-menu-close");

        if ($mobileMenuClose.length) {
            $mobileMenuClose.on("click", function () {
                $mobileMenu.removeClass("active");
                $("body").removeClass("menu-open");
                $mobileMenuToggle.attr("aria-expanded", false);
            });
        }

        /* ========================
   🎯 Owl Nav Fixed to Screen Edges (Global)
======================== */
        (function () {
            function moveAllNavsToBody() {
                $(".owl-carousel")
                    .not(".welcome, .upsell, .pa-minicart")
                    .each(function (index) {
                        const $carousel = $(this);
                        const $nav = $carousel.find(".owl-nav");
                        if (!$nav.length || $nav.data("moved")) return;

                        $nav.data("moved", true);
                        $("body").append($nav);

                        $nav.css({
                            position: "fixed",
                            inset: 0, // shorthand for top/right/bottom/left = 0
                            width: "100%",
                            height: "100%",
                            pointerEvents: "none", // ✅ let swipe/touch go through
                            zIndex: 999,
                        });

                        $nav.find("button").css({
                            pointerEvents: "auto !important", // ✅ only buttons receive clicks
                            position: "fixed",
                            borderRadius: "50%",
                            backdropFilter: "blur(10px)",
                            background: "rgba(255,255,255,0.7)",
                            border: "none",
                            boxShadow: "0 4px 10px rgba(0,0,0,0.15)",
                            display: "flex",
                            alignItems: "center",
                            justifyContent: "center",
                            cursor: "pointer",
                            zIndex: 10000,
                            padding: 0,
                        });
                    });

                updateNavPositions();
            }

            function updateNavPositions() {
                $(".owl-carousel")
                    .not(".welcome, .upsell, .pa-minicart")
                    .each(function (i) {
                        const $carousel = $(this);
                        const rect = this.getBoundingClientRect();
                        const $nav = $("body")
                            .find(".owl-nav")
                            .filter(function () {
                                return $(this).data("moved");
                            })
                            .eq(i);
                        const $prev = $nav.find(".owl-prev");
                        const $next = $nav.find(".owl-next");
                        const offset = 16;

                        // Only calculate top if carousel is visible
                        const visible =
                            rect.bottom > 0 && rect.top < window.innerHeight;
                        if (!visible) {
                            $prev.css("opacity", 0);
                            $next.css("opacity", 0);
                            return;
                        }

                        // Center vertically relative to viewport
                        const centerY = rect.top + rect.height / 2;
                        const topValue = Math.max(
                            44,
                            Math.min(window.innerHeight - 44, centerY)
                        );

                        $prev.css({
                            left: `${offset}px`,
                            top: `${topValue}px`,
                            transform: "translateY(-50%)",
                            opacity: 0.5,
                        });
                        $next.css({
                            right: `${offset}px`,
                            top: `${topValue}px`,
                            transform: "translateY(-50%)",
                            opacity: 0.5,
                        });
                    });
            }

            $(window).on("scroll resize", updateNavPositions);

            const observer = new MutationObserver(() => moveAllNavsToBody());
            observer.observe(document.body, { childList: true, subtree: true });

            $(window).on("load", () => setTimeout(moveAllNavsToBody, 600));
        })();

        /* ========================
   🎯 Replace Carousel Nav Arrows (Owl + Slick) with SVGs
======================== */
        const prevSVG = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" width="50" height="50">
  <path d="M21.559,12.062 L15.618,17.984 L21.5221,23.944 C22.105,24.533 22.1021,25.482 21.5131,26.065 C21.2211,26.355 20.8391,26.4999987 20.4571,26.4999987 C20.0711,26.4999987 19.6851,26.352 19.3921,26.056 L12.4351,19.034 C11.8531,18.446 11.8551,17.4999987 12.4411,16.916 L19.4411,9.938 C20.0261,9.353 20.9781,9.354 21.5621,9.941 C22.1471,10.528 22.1451,11.478 21.5591,12.062 Z"></path>
</svg>`;

        const nextSVG = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" width="50" height="50">
  <path d="M23.5587,16.916 C24.1447,17.4999987 24.1467,18.446 23.5647,19.034 L16.6077,26.056 C16.3147,26.352 15.9287,26.4999987 15.5427,26.4999987 C15.1607,26.4999987 14.7787,26.355 14.4867,26.065 C13.8977,25.482 13.8947,24.533 14.4777,23.944 L20.3818,17.984 L14.4408,12.062 C13.8548,11.478 13.8528,10.5279 14.4378,9.941 C15.0218,9.354 15.9738,9.353 16.5588,9.938 L23.5588,16.916 Z"></path>
</svg>`;

        function replaceCarouselArrows() {
            /* 🦉 Owl Carousel */
            $(".owl-carousel").each(function () {
                const $carousel = $(this);
                const $prev = $carousel.find(
                    '.owl-prev span[aria-label="Previous"]'
                );
                const $next = $carousel.find(
                    '.owl-next span[aria-label="Next"]'
                );
                if ($prev.length) $prev.replaceWith(prevSVG);
                if ($next.length) $next.replaceWith(nextSVG);
            });

            /* 🧊 Slick Slider (Magento PageBuilder) */
            $(".pagebuilder-slider.slick-initialized").each(function () {
                const $slider = $(this);
                const $prev = $slider.find(".slick-prev");
                const $next = $slider.find(".slick-next");

                // Replace content if not already an SVG
                if ($prev.length && !$prev.find("svg").length)
                    $prev.html(prevSVG);
                if ($next.length && !$next.find("svg").length)
                    $next.html(nextSVG);
            });
        }

        /* Run once on DOM ready and again after sliders initialize */
        $(document).ready(function () {
            replaceCarouselArrows();
        });

        // Optional: If some sliders initialize dynamically later (Magento does this)
        $(document).on(
            "init reInit afterChange",
            ".pagebuilder-slider",
            function () {
                replaceCarouselArrows();
            }
        );

        /* ========================
   🧩 Show aa-Panel only when it has content
======================== */
        function toggleAaPanelVisibility() {
            const $panel = $(".aa-Panel");
            if (!$panel.length) return;

            // Check if panel has visible content (items, suggestions, etc.)
            const hasContent =
                $panel.find(".aa-Item, .aa-Source, .aa-List").children()
                    .length > 0;

            // Toggle visibility
            if (hasContent) {
                $panel.addClass("is-ready");
            } else {
                $panel.removeClass("is-ready");
            }
        }

        /* Observe aa-Panel changes */
        const aaObserver = new MutationObserver(toggleAaPanelVisibility);
        aaObserver.observe(document.body, { childList: true, subtree: true });

        /* Initial check (for good measure) */
        toggleAaPanelVisibility();

        /* ========================
   ⚪ Owl Carousel – Sliding Active Dot Indicator (Round)
======================== */
        $(document).on("initialized.owl.carousel", function (event) {
            const $carousel = $(event.target);
            const $dotsContainer = $carousel.find(".owl-dots");

            if (
                $dotsContainer.length &&
                !$dotsContainer.find(".dot-indicator").length
            ) {
                const $firstDot = $dotsContainer.find(".owl-dot").first();
                if ($firstDot.length) {
                    const size = $firstDot.outerWidth();
                    const offset = $firstDot.position().left;
                    $dotsContainer.append(`<span class="dot-indicator" style="
        position:absolute;
        top:0;
        left:${offset}px;
        width:${size}px;
        height:${size}px;
        border-radius:50%;
        background:#000;
        transform:translateX(0);
        transition:transform 0.4s cubic-bezier(0.4,0,0.2,1);
        z-index:2;
      "></span>`);
                }
            }
        });

        $(document).on("changed.owl.carousel", function (event) {
            const $carousel = $(event.target);
            const index = event.item.index || 0;
            const $dots = $carousel.find(".owl-dot");
            const $indicator = $carousel.find(".dot-indicator");
            if (!$dots.length || !$indicator.length) return;

            const $targetDot = $dots.eq(index);
            if (!$targetDot.length) return;

            const moveX = $targetDot.position().left;
            $indicator.css("transform", `translateX(${moveX}px)`);
        });

        /* ========================
    🩹 Keep aa-Panel perfectly aligned under Sticky Header (Web only)
 ======================== */
        function alignAaPanel() {
            if (window.innerWidth <= 768) return; // 👈 Skip entirely on mobile

            const $panel = $(".aa-Panel");
            const $input = $("#autocomplete-0-input");
            const $header = $(".header.content");

            if (!$panel.length || !$input.length || !$header.length) return;

            const inputOffset = $input.offset();
            const headerHeight = $header.outerHeight() || 0;
            const scrollTop = $(window).scrollTop();
            const inputTop = inputOffset.top - scrollTop;

            const newTop = $header.hasClass("is-sticky")
                ? headerHeight + 1
                : inputTop + $input.outerHeight() + 1;

            const newLeft = inputOffset.left;
            const newWidth = $input.outerWidth();

            if ($header.hasClass("is-sticky")) {
                $panel.css({
                    position: "fixed",
                    top: `${newTop}px`,
                    left: `${newLeft}px`,
                    right: "unset",
                    zIndex: 10000,
                    marginTop: 0,
                    width: `${newWidth}px`,
                });
            } else {
                // When not sticky, revert to Algolia’s normal flow
                $panel.attr("style", "");
            }
        }

        /* ========================
    🧠 Reactive Updates (Desktop only)
 ======================== */
        function initAaPanelAlignment() {
            if (window.innerWidth > 768) {
                $(window).on("scroll resize", alignAaPanel);
                $(document).on(
                    "input focus",
                    "#autocomplete-0-input",
                    alignAaPanel
                );

                // Observe DOM since Algolia dynamically replaces the panel
                const aaStickObserver = new MutationObserver(alignAaPanel);
                aaStickObserver.observe(document.body, {
                    childList: true,
                    subtree: true,
                });

                // Save observer so we can disconnect on mobile if needed
                window.aaStickObserver = aaStickObserver;
            } else if (window.aaStickObserver) {
                // 👋 Clean up when switching to mobile
                window.aaStickObserver.disconnect();
                $(window).off("scroll resize", alignAaPanel);
                $(document).off(
                    "input focus",
                    "#autocomplete-0-input",
                    alignAaPanel
                );
            }
        }

        // Initialize and re-check on resize
        initAaPanelAlignment();
        $(window).on("resize", initAaPanelAlignment);

        /* ========================
            🖐️ Slick Slider – 2-Finger Swipe (Trackpad)
         ======================== */
         (function() {
             $(document).on(
                 "wheel",
                 ".pagebuilder-slider.slick-slider",
                 function (e) {
                     const event = e.originalEvent;
                     const $slider = $(this);

                     // Detect horizontal gesture with minimum threshold
                     if (Math.abs(event.deltaX) > Math.abs(event.deltaY) && Math.abs(event.deltaX) > 3) {
                         e.preventDefault();
                         if (!$slider.hasClass("slick-initialized")) return;

                         // Per-slider tracking
                         if (!$slider.data('swiping')) {
                             $slider.data('swiping', true);

                             if (event.deltaX > 0) {
                                 $slider.slick("slickNext");
                             } else {
                                 $slider.slick("slickPrev");
                             }
                         }

                         // Reset with optimized timing
                         clearTimeout($slider.data('swipeTimeout'));
                         const timeout = setTimeout(() => {
                             $slider.data('swiping', false);
                         }, 120); // Sweet spot: fast enough for consecutive swipes, slow enough to prevent jumps
                         $slider.data('swipeTimeout', timeout);
                     }
                 }
             );
         })();

        /* ========================
   🌀 TCL Banner – Two-Finger Swipe Only (No CSS / No Click)
      ========================
*/
        $(function () {
            // Settings
            const SWIPE_THRESHOLD = 50; // px
            const LOCK_MS = 300; // prevent repeated triggers
            const WHEEL_MIN = 10; // wheel threshold to consider as horizontal swipe

            function initBanner($carousel) {
                if (!$carousel || !$carousel.length) return;

                const slidesContainer = $carousel[0].querySelector(
                    ".tcl-banner__slides-container"
                );
                const slides = Array.from(
                    $carousel[0].querySelectorAll(".tcl-banner__slide")
                );
                const dots = Array.from(
                    $carousel[0].querySelectorAll(".tds-tab")
                );

                if (!slidesContainer || !slides.length) return;

                // find initial active index from dots (aria-selected) or first slide shown
                let activeIndex = 0;
                const selectedDotIndex = dots.findIndex(
                    (d) => d.getAttribute("aria-selected") === "true"
                );
                if (selectedDotIndex >= 0) activeIndex = selectedDotIndex;

                let twoFinger = false;
                let touchStartX = 0;
                let locked = false;

                function setActive(index) {
                    index =
                        ((index % slides.length) + slides.length) %
                        slides.length;
                    slides.forEach((slide, i) =>
                        slide.classList.toggle(
                            "tcl-banner__slide--active",
                            i === index
                        )
                    );
                    dots.forEach((dot, i) =>
                        dot.setAttribute(
                            "aria-selected",
                            i === index ? "true" : "false"
                        )
                    );
                    activeIndex = index;
                }

                // initialize visible slide
                setActive(activeIndex);

                // Helper to lock briefly after a swipe to avoid multiple triggers
                function lockTemporary() {
                    locked = true;
                    setTimeout(() => {
                        locked = false;
                    }, LOCK_MS);
                }

                // --- Touch handlers (two-finger) ---
                function onTouchStart(e) {
                    const touches =
                        e.touches ||
                        (e.originalEvent && e.originalEvent.touches);
                    if (!touches) return;
                    if (touches.length === 2) {
                        twoFinger = true;
                        touchStartX =
                            (touches[0].clientX + touches[1].clientX) / 2;
                    } else {
                        twoFinger = false;
                    }
                }

                function onTouchMove(e) {
                    if (!twoFinger || locked) return;
                    const touches =
                        e.touches ||
                        (e.originalEvent && e.originalEvent.touches);
                    if (!touches || touches.length !== 2) return;

                    const touchEndX =
                        (touches[0].clientX + touches[1].clientX) / 2;
                    const deltaX = touchEndX - touchStartX;

                    // horizontal swipe detection only
                    if (Math.abs(deltaX) > SWIPE_THRESHOLD) {
                        // prevent vertical scroll when we trigger a slide change
                        if (e.cancelable) e.preventDefault();

                        if (deltaX < 0) {
                            // left -> next
                            setActive((activeIndex + 1) % slides.length);
                        } else {
                            // right -> prev
                            setActive(
                                (activeIndex - 1 + slides.length) %
                                slides.length
                            );
                        }
                        twoFinger = false;
                        lockTemporary();
                    }
                }

                function onTouchEnd() {
                    twoFinger = false;
                }

                // Add non-passive listeners so we can call preventDefault()
                slidesContainer.addEventListener("touchstart", onTouchStart, {
                    passive: true,
                });
                slidesContainer.addEventListener("touchmove", onTouchMove, {
                    passive: false,
                });
                slidesContainer.addEventListener("touchend", onTouchEnd, {
                    passive: true,
                });
                slidesContainer.addEventListener("touchcancel", onTouchEnd, {
                    passive: true,
                });

                // --- Wheel/trackpad support for horizontal two-finger swipes ---
                // Use the carousel element so the listener is scoped
                function onWheel(e) {
                    // Prefer deltaX if horizontal; ignore small moves
                    const ev = e || window.event;
                    const absX = Math.abs(ev.deltaX || 0);
                    const absY = Math.abs(ev.deltaY || 0);

                    // require horizontal-dominant and over threshold
                    if (absX > absY && absX > WHEEL_MIN && !locked) {
                        // prevent page horizontal scroll
                        if (e.cancelable) e.preventDefault();

                        if ((ev.deltaX || 0) > 0) {
                            // scrolled left -> go next
                            setActive((activeIndex + 1) % slides.length);
                        } else {
                            // scrolled right -> prev
                            setActive(
                                (activeIndex - 1 + slides.length) %
                                slides.length
                            );
                        }
                        lockTemporary();
                    }
                }

                // add wheel listener directly (not through jQuery) and non-passive so preventDefault works
                slidesContainer.addEventListener("wheel", onWheel, {
                    passive: false,
                });

                // Optional: clicking the dots should update activeIndex (keep behavior consistent)
                dots.forEach((dot, i) => {
                    dot.addEventListener("click", function (ev) {
                        ev.preventDefault && ev.preventDefault();
                        setActive(i);
                        lockTemporary();
                    });
                });
            }

            // Initialize all tcl-banner__carousel instances when DOM ready.
            // If carousels may be rendered later, observe the DOM too.
            function initAll() {
                const $carousels = $(".tcl-banner__carousel");
                $carousels.each(function () {
                    initBanner($(this));
                });
            }

            initAll();

            // If pagebuilder or other scripts may insert banners after load, watch for additions
            if (window.MutationObserver) {
                const mo = new MutationObserver((mutations) => {
                    for (const m of mutations) {
                        if (m.addedNodes && m.addedNodes.length) {
                            // cheap detection: any added node that contains the banner class
                            const found = Array.from(m.addedNodes).some((n) => {
                                return (
                                    n.nodeType === 1 &&
                                    (n.matches(".tcl-banner__carousel") ||
                                        (n.querySelector &&
                                            n.querySelector(
                                                ".tcl-banner__carousel"
                                            )))
                                );
                            });
                            if (found) {
                                initAll();
                                break;
                            }
                        }
                    }
                });
                mo.observe(document.body, { childList: true, subtree: true });
            }
        });

        /* ========================
   🎯 Slick Dots Animated Backdrop
======================== */
        $(document).ready(function () {
            function initBackdrop($slider) {
                const $dots = $slider.find(".slick-dots");
                if (!$dots.length) return;

                // ensure container positioning
                $dots.css("position", "relative");

                // inject backdrop once
                let $backdrop = $dots.find(".slick--animated-backdrop");
                if (!$backdrop.length) {
                    $backdrop = $(
                        '<div class="slick--animated-backdrop"></div>'
                    );
                    // ✅ Start hidden
                    $backdrop.css({
                        opacity: "0",
                        transition: "none",
                    });
                    $dots.append($backdrop);
                }

                function moveBackdrop(animate = true) {
                    const $activeLi = $dots.find("li.slick-active");
                    if (!$activeLi.length) return;

                    const liOffset = $activeLi.position()?.left || 0;
                    const liWidth = $activeLi.outerWidth() || 0;

                    // temporarily disable transition for instant placement
                    if (!animate) $backdrop.css("transition", "none");

                    $backdrop.css({
                        transform: `translateX(${liOffset}px)`,
                        width: liWidth + "px",
                        opacity: "1",
                    });

                    // restore transition after instant placement
                    if (!animate) {
                        setTimeout(() => {
                            $backdrop.css("transition", "");
                        }, 50);
                    }
                }

                // ✅ Wait for layout to fully settle, then position backdrop
                setTimeout(() => {
                    moveBackdrop(false);
                }, 300); // Increased delay

                // then re-enable smooth motion for future changes
                $slider.on("afterChange", () => moveBackdrop(true));
                $(window).on("resize", () => moveBackdrop(true));

                // dot click updates backdrop visually even before slide change
                $dots.on("click", "button", function () {
                    setTimeout(() => moveBackdrop(true), 150);
                });
            }

            // Wait for Slick to initialize and dots to appear
            const checkSlick = setInterval(function () {
                const $sliders = $(".pagebuilder-slider.slick-initialized");
                if ($sliders.length && $sliders.find(".slick-dots").length) {
                    clearInterval(checkSlick);
                    $sliders.each(function () {
                        initBackdrop($(this));
                    });
                }
            }, 200);

            // ✅ CRITICAL FIX: Recalculate on window load (after all resources loaded)
            $(window).on("load", function () {
                $(".pagebuilder-slider.slick-initialized").each(function () {
                    const $slider = $(this);
                    const $dots = $slider.find(".slick-dots");
                    const $backdrop = $dots.find(".slick--animated-backdrop");
                    const $activeLi = $dots.find("li.slick-active");

                    if ($backdrop.length && $activeLi.length) {
                        const liOffset = $activeLi.position()?.left || 0;
                        const liWidth = $activeLi.outerWidth() || 0;

                        $backdrop.css({
                            transform: `translateX(${liOffset}px)`,
                            width: liWidth + "px",
                            opacity: "1",
                            transition: "none",
                        });

                        setTimeout(() => {
                            $backdrop.css("transition", "");
                        }, 50);
                    }
                });
            });
        });

        /* ========================
 🎯 Owl Dots Tesla-Style Backdrop (Rounded Highlight)
======================== */
        $(document).ready(function () {
            function initOwlBackdrop($carousel) {
                const $dots = $carousel.find(".owl-dots");
                if (!$dots.length) return;

                $dots.css("position", "relative");

                // Ensure backdrop exists
                let $backdrop = $dots.find(".owl--animated-backdrop");
                if (!$backdrop.length) {
                    $backdrop = $('<div class="owl--animated-backdrop"></div>');
                    $dots.append($backdrop);
                }

                function moveBackdrop($dot, animate = true) {
                    if (!$dot?.length) return;

                    const dotOffset = $dot.position()?.left || 0;
                    const dotWidth = $dot.outerWidth() || 0;
                    const dotHeight = $dot.outerHeight() || 0;

                    if (!animate) $backdrop.css("transition", "none");

                    // Center the backdrop behind the dot
                    $backdrop.css({
                        transform: `translateX(${dotOffset}px)`,
                        width: `${dotWidth}px`,
                        height: `${dotHeight}px`,
                    });

                    if (!animate)
                        setTimeout(() => $backdrop.css("transition", ""), 50);
                }

                // Initial placement
                setTimeout(
                    () => moveBackdrop($dots.find(".owl-dot.active"), false),
                    100
                );

                // On dot click → instant move
                $dots.on("click", ".owl-dot", function () {
                    moveBackdrop($(this), true);
                });

                // On carousel slide change
                $carousel.on("changed.owl.carousel", function () {
                    // Re-inject backdrop if Owl rebuilt the dots
                    if (!$dots.find(".owl--animated-backdrop").length) {
                        $dots.append($backdrop);
                    }
                    moveBackdrop($dots.find(".owl-dot.active"), true);
                });

                // On resize
                $(window).on("resize", function () {
                    moveBackdrop($dots.find(".owl-dot.active"), true);
                });
            }

            // Wait for Owl to initialize
            const checkOwl = setInterval(() => {
                const $carousels = $(".owl-carousel.owl-loaded");
                if ($carousels.length && $carousels.find(".owl-dots").length) {
                    clearInterval(checkOwl);
                    $carousels.each(function () {
                        initOwlBackdrop($(this));
                    });
                }
            }, 200);
        });

        //Minicart Remove Confirmation Change Text

        $(function () {
            // Watch for dynamically created confirm popups
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    $(mutation.addedNodes).each(function () {
                        const $node = $(this);

                        // Check if a confirm modal has appeared
                        if (
                            $node.hasClass("modal-popup") &&
                            $node.hasClass("confirm") &&
                            $node.find(
                                '.modal-content:contains("remove this item")'
                            ).length
                        ) {
                            // ✳️ Change question text
                            $node
                                .find(".modal-content div")
                                .text(
                                    "Are you sure you would like to remove this item?"
                                );
                            // Update button labels
                            $node
                                .find(".action-secondary.action-dismiss span")
                                .text("No, Keep It");
                            $node
                                .find(".action-primary.action-accept span")
                                .text("Yes, Remove It");

                            // Optional: focus No button
                            $node
                                .find(".action-secondary.action-dismiss")
                                .focus();
                        }
                    });
                });
            });

            // Observe the whole body for new modal popups
            observer.observe(document.body, { childList: true, subtree: true });
        });

        //Change Proceed To Checkout Text

        // Wait for minicart or cart page to load fully
        const checkoutBtnInterval = setInterval(function () {
            // 🛒 Mini cart button
            const $miniBtn = $(
                ".action.primary.checkout, .checkout.methods .action.checkout"
            );
            // 🛍️ Cart page button
            const $cartBtn = $(
                ".cart-summary .checkout-methods-items .action.primary.checkout"
            );

            if ($miniBtn.length || $cartBtn.length) {
                $miniBtn.text("Proceed to Checkout");
                $cartBtn.text("Proceed to Checkout");

                // Optionally also update the title attribute (for accessibility)
                $miniBtn.attr("title", "Proceed to Checkout");
                $cartBtn.attr("title", "Proceed to Checkout");

                clearInterval(checkoutBtnInterval);
            }
        }, 300);

        //Modal close isssue
        // 🧩 Intercept and kill Magento's fade animations globally
        $.widget("mage.modal", $.mage.modal, {
            _fade: function (isIn, callback) {
                // Instantly toggle visibility — no animation delay
                if (isIn) {
                    this.element.show();
                } else {
                    this.element.hide();
                }
                console.log("callback");
                if (typeof callback === "function") callback.call(this);
            },
        });

        // 🧹 Ensure any existing confirm modals also close instantly
        $(document)
            .on("modalclosed", function () {
                console.log("modalclosed");
                $(".modal-popup.confirm").each(function () {
                    const $modal = $(this);
                    $modal.stop(true, true).hide().removeClass("_show _hidden");
                });
                $(".modals-overlay").removeClass("_show _active").hide();
                $("body").removeClass("_has-modal");
            })
            .on(
                "click",
                '.modal-popup.confirm [data-role="action"], .modal-popup.confirm [data-role="closeBtn"]',
                function () {
                    console.log("modal on click");
                    const $modal = $(this).closest(".modal-popup.confirm");
                    $modal.stop(true, true).hide().removeClass("_show _hidden");
                    $(".modals-overlay").removeClass("_show _active").hide();
                    $("body").removeClass("_has-modal");
                }
            );

        // Show Upsell PA Pop Up Widget (with 2s delay)
        $(
            ".tocart-pdp-btn, #zip-product-widget, .pdp-afterpay-logo, .add-to-cart-trigger"
        ).on("click", function () {
            //setTimeout(function () {
            $("#pa-upsell").addClass("active");
            $(".minicart-overlay").css("display", "block"); // 🔹 Sync overlay on show
            //}, 2000) // 2s delay
        });

        // 🔄 Keep overlay consistent with pa-upsell active state
        const paUpsellObserver = new MutationObserver(() => {
            const isActive = $("#pa-upsell").hasClass("active");
            const $miniOverlay = $(".minicart-overlay");

            if (isActive) {
                $miniOverlay.css("display", "block");
            } else {
                // Only hide overlay if minicart is not visible
                const $minicartDropdown = $(
                    '.block-minicart[data-role="dropdownDialog"]'
                );
                const minicartVisible =
                    $minicartDropdown.length &&
                    $minicartDropdown.is(":visible") &&
                    $minicartDropdown.css("display") !== "none";

                if (!minicartVisible) {
                    $miniOverlay.css("display", "none");
                }
            }
        });

        // Observe pa-upsell for active class changes
        if (document.querySelector("#pa-upsell")) {
            paUpsellObserver.observe(document.querySelector("#pa-upsell"), {
                attributes: true,
                attributeFilter: ["class"],
            });
        }

        //PA Upsell Widget Checked Default
        $(window).on("load", function () {
            $(".pa-bundle-product").prop("checked", true).trigger("change");
        });

        //Select all pa upsell products
        $(document).on("click", "#upsell-select-all", function () {
            $(".pa-checkbox-input.pa-bundle-product")
                .prop("checked", true)
                .trigger("change");
        });

        // 🧩 Upsell Add All Checked
        $(document).on("click", "#upsell-add-to-cart-all", function (e) {
            e.preventDefault(); // ✅ stops form submission / link navigation
            e.stopPropagation(); // ✅ prevents parent handlers from closing upsell

            const $checked = $(".pa-bundle-product:checked");

            // 🧩 If no products are selected
            if ($checked.length === 0) {
                $("#custom-alert").css("display", "block");
                // Keep upsell open visually
                $("#pa-upsell").addClass("active");
                return;
            }

            // 🧩 If products are selected, proceed with adding
            // Remove upsell popup when user confirms adding products
            $("#pa-upsell").removeClass("active");

            const items = $checked.toArray();
            const startCount = customerData.get("cart")()?.summary_count || 0;
            let addingMultiple = true;

            function addNext(index) {
                if (index >= items.length) {
                    console.log(
                        "🛒 All products processed. Finalizing cart..."
                    );
                    finalizeCartUpdate();
                    return;
                }

                const $checkbox = $(items[index]);
                const sku = $checkbox.data("product-sku");
                const $form = $(`form[data-product-sku="${sku}"]`);

                if (!$form.length) {
                    console.warn(`⚠️ No form found for SKU ${sku}`);
                    addNext(index + 1);
                    return;
                }

                $.ajax({
                    url: $form.attr("action"),
                    type: "POST",
                    data: $form.serialize(),
                    showLoader: index === 0,
                })
                    .done(() => {
                        console.log(`✅ Added SKU ${sku} to cart`);
                        addNext(index + 1);
                    })
                    .fail((xhr) => {
                        console.error(`❌ Failed to add SKU ${sku}:`, xhr);
                        addNext(index + 1);
                    });
            }

            function finalizeCartUpdate() {
                customerData.invalidate(["cart"]);
                customerData.reload(["cart"], true);

                const interval = setInterval(() => {
                    const updatedCount =
                        customerData.get("cart")()?.summary_count || 0;
                    if (updatedCount > startCount) {
                        clearInterval(interval);
                        console.log(
                            `✅ Cart count updated from ${startCount} → ${updatedCount}`
                        );
                        addingMultiple = false;

                        if (!$("#pa-upsell").hasClass("active")) {
                            const $minicart = $(".action.showcart");
                            if (!$minicart.hasClass("active")) {
                                $minicart.trigger("click");
                                console.log(
                                    "🛒 Minicart opened (upsell inactive)"
                                );
                            }
                        }
                    }
                }, 400);

                setTimeout(() => clearInterval(interval), 10000);
            }

            // Start adding items sequentially
            addNext(0);
        });

        // 🧩 Close custom alert
        $(document).on("click", "#custom-alert-close", function () {
            $("#custom-alert").css("display", "none");
        });

        /* 🧩 Simple custom message behavior */
        $(document).on("click", "#custom-alert-close", function () {
            $("#custom-alert").css("display", "none");
        });

        // Close PA Upsell Widget
        $("#upsell-close, #upsell-widget-desktop .action.tocart.primary").on(
            "click",
            function () {
                $("#pa-upsell").removeClass("active");
            }
        );

        // ======================
        // 🔁 AJAX Cart Update Helper
        // ======================
        function updateCartAjax($input, newQty) {
            const itemIdMatch = $input.attr("name")?.match(/\[(\d+)\]/);
            if (!itemIdMatch) {
                console.error("❌ Cannot extract item ID from", $input.attr("name"));
                return;
            }

            const itemId = itemIdMatch[1];

            // ✅ Find and disable both +/- buttons for this item
            var $qtyButtons  = $input.closest('.qty-buttons');
            var $btnIncrease = $qtyButtons.find('.qty-increase-cart-page');
            var $btnDecrease = $qtyButtons.find('.qty-decrease-cart-page');

            function disableButtons() {
                $btnIncrease.css({ 'opacity': '0.4', 'pointer-events': 'none', 'cursor': 'not-allowed' });
                $btnDecrease.css({ 'opacity': '0.4', 'pointer-events': 'none', 'cursor': 'not-allowed' });
            }

            function enableButtons() {
                $btnIncrease.css({ 'opacity': '', 'pointer-events': '', 'cursor': '' });
                $btnDecrease.css({ 'opacity': '', 'pointer-events': '', 'cursor': '' });
            }

            disableButtons();

            require([
                "mage/url",
                "Magento_Checkout/js/model/cart/totals-processor/default",
                "Magento_Checkout/js/model/quote",
            ], function (urlBuilder, totalsProcessor, quote) {
                const updateUrl = urlBuilder.build("checkout/cart/updatePost/");

                $.ajax({
                    url: updateUrl,
                    type: "POST",
                    data: {
                        form_key: $('input[name="form_key"]').val(),
                        [`cart[${itemId}][qty]`]: newQty,
                        update_cart_action: "update_qty",
                    },
                    beforeSend: function () {
                        $input.prop("disabled", true);
                    },
                    success: function () {
                        customerData.invalidate(["cart", "checkout-data"]);
                        customerData.reload(["cart", "checkout-data"], true);

                        try {
                            totalsProcessor.estimateTotals(quote);
                        } catch (err) {
                            console.warn("⚠️ Could not run totalsProcessor:", err);
                        }

                        // ✅ Wait for cart section to reflect the update before re-enabling
                        var checkCount  = 0;
                        var maxChecks   = 20;
                        var checkInterval = setInterval(function () {
                            checkCount++;
                            var cartData     = customerData.get('cart')();
                            var updatedTotal = 0;

                            if (cartData && cartData.items) {
                                $.each(cartData.items, function (i, item) {
                                    updatedTotal += parseInt(item.qty) || 0;
                                });
                            }

                            // Re-enable once cart data has refreshed or timeout reached
                            if (updatedTotal > 0 || checkCount >= maxChecks) {
                                clearInterval(checkInterval);
                                enableButtons();
                            }
                        }, 300);
                    },
                    error: function (xhr, status, err) {
                        console.error("❌ Cart update failed", status, err);
                        enableButtons(); // ✅ Always re-enable on error
                    },
                    complete: function () {
                        $input.prop("disabled", false);
                    },
                });
            });
        }

        // ======================
        // 🧮 Qty Button Click Logic
        // ======================
        document.addEventListener(
            "click",
            function (e) {
                const btn = e.target.closest(
                    ".qty-increase-cart-page, .qty-decrease-cart-page"
                );
                if (!btn) return;

                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                var $btn        = $(btn);
                var $qtyButtons = $btn.closest('.qty-buttons');
                var $input      = $qtyButtons.find('input[data-role="cart-item-qty"]');
                var $display    = $qtyButtons.find('.qty-display');

                if (!$input.length) return;

                var currentVal = parseInt($input.val()) || 1;
                var newVal = btn.classList.contains('qty-increase-cart-page')
                    ? currentVal + 1
                    : Math.max(1, currentVal - 1);

                // ✅ Validation — cart page only
                if (document.body.classList.contains('checkout-cart-index')) {
                    var GLOBAL_CART_LIMIT  = 10;
                    var productLimit       = parseInt($input.attr('data-order-limit')) || GLOBAL_CART_LIMIT;
                    var totalCartQty       = 0;

                    $('input[data-role="cart-item-qty"]').each(function () {
                        totalCartQty += parseInt($(this).val()) || 0;
                    });

                    var projectedTotal = (totalCartQty - currentVal) + newVal;
                    var errorMsg       = null;

                    if (newVal > productLimit) {
                        errorMsg = 'The maximum you may purchase of this item is ' + productLimit + '.';
                    } else if (projectedTotal > GLOBAL_CART_LIMIT) {
                        var allowedForItem = GLOBAL_CART_LIMIT - (totalCartQty - currentVal);
                        errorMsg = 'Cart limit ' + GLOBAL_CART_LIMIT + '. Max ' + Math.max(0, allowedForItem) + ' here.';
                    }

                    if (errorMsg) {
                        var $field = $input.closest('.field.qty');
                        var $error = $field.find('.cart-qty-error');
                        if (!$error.length) {
                            $error = $('<div class="cart-qty-error mage-error" style="color:#e02b27;font-size:12px;margin-top:4px;"></div>');
                            $input.closest('.qty-buttons').after($error);
                        }
                        $error.text(errorMsg).show();
                        return;
                    }

                    // Clear error if valid
                    $input.closest('.field.qty').next('.cart-qty-error').hide();
                }

                // ✅ Update hidden input
                $input.val(newVal);

                // ✅ Update visible display span (cart page)
                if ($display.length) {
                    $display.text(newVal);
                }

                // ✅ AJAX update
                updateCartAjax($input, newVal);
            },
            true
        );

        //Fix Carousel On Android Mobile
        $(document).ready(function () {
            const $carousel = $(".owl-carousel");

            let imagesLoadedCount = 0;
            const totalImages = $carousel.find("img").length;

            $carousel.find("img").each(function () {
                if (this.complete) {
                    imagesLoadedCount++;
                } else {
                    $(this).on("load", function () {
                        imagesLoadedCount++;
                        if (imagesLoadedCount === totalImages) {
                            $carousel.trigger("refresh.owl.carousel");
                        }
                    });
                }
            });

            $(window).on("resize", function () {
                $carousel.trigger("refresh.owl.carousel");
            });
        });

        //Hide PA Minicart Widget If Cart Is Empty
        function toggleMiniUpsell() {
            const $minicart = $(".block-minicart");
            const $upsell = $(".pa-minicart-widget");

            if ($minicart.find(".empty-cart").length > 0) {
                $upsell.addClass("is-hidden");
            } else {
                $upsell.removeClass("is-hidden");
            }
        }

        // Observe minicart for updates
        const minicartEl = document.querySelector(".block-minicart");
        if (minicartEl) {
            const upsellObserver = new MutationObserver(() => {
                toggleMiniUpsell();
            });
            upsellObserver.observe(minicartEl, {
                childList: true,
                subtree: true,
            });
        }

        // Initial check on load
        $(document).ready(toggleMiniUpsell);

        //Takeover Banner, Header, AA Panel Fix
        (function () {
            var styleId = "aa-panel-dynamic-style";
            var lastHeaderHeight = null;

            function updateAaPanelCSS() {
                var $header = $(".page-header");

                // Always remove style if mobile or no header
                if (!$header.length || window.innerWidth <= 768) {
                    $("#" + styleId).remove();
                    lastHeaderHeight = null;
                    return;
                }

                var headerHeight = $header.outerHeight() || 0;

                // Only update if height changed
                if (lastHeaderHeight === headerHeight) {
                    return;
                }

                lastHeaderHeight = headerHeight;
                var cssRule =
                    ".aa-Panel { top: " + headerHeight + "px !important; }";

                // Remove old style and create new one
                $("#" + styleId).remove();
                $(
                    '<style id="' + styleId + '">' + cssRule + "</style>"
                ).appendTo("head");

                console.log("AA Panel CSS updated - top:", headerHeight + "px"); // Debug log
            }

            // Debounce helper
            function debounce(fn, wait) {
                var t;
                return function () {
                    clearTimeout(t);
                    t = setTimeout(fn, wait);
                };
            }

            $(document).ready(function () {
                // Force initial update
                setTimeout(updateAaPanelCSS, 0);
                setTimeout(updateAaPanelCSS, 100);
                setTimeout(updateAaPanelCSS, 500);

                // Continuous monitoring - check every 100ms
                setInterval(updateAaPanelCSS, 100);

                // Also on resize and scroll
                $(window).on("resize", debounce(updateAaPanelCSS, 50));
                $(window).on("scroll", debounce(updateAaPanelCSS, 100));
            });
        })();

        function closeAlgolia() {
            // Close autocomplete cleanly - works on mobile and desktop
            if (
                window.algoliaAutocompleteInstance &&
                typeof window.algoliaAutocompleteInstance.setIsOpen ===
                "function"
            ) {
                // Use Algolia's built-in method to close properly
                window.algoliaAutocompleteInstance.setIsOpen(false);
                console.log("[Algolia] Autocomplete closed via setIsOpen ✅");

                // Blur the search input
                const input = document.querySelector(
                    'input[type="search"], .aa-Input'
                );
                if (input) {
                    input.blur();

                    // Mobile keyboard dismissal
                    if (document.activeElement === input) {
                        input.setAttribute("readonly", "readonly");
                        setTimeout(function () {
                            input.removeAttribute("readonly");
                            input.blur();
                        }, 100);
                    }
                }
            } else {
                console.warn("[Algolia] Autocomplete instance not available");
            }
        }

        $(
            ".panel.wrapper, .mobile-menu-icon, .header.content .logo, .minicart-wrapper"
        ).on("click", function (e) {
            //e.stopPropagation();
            closeAlgolia();
            console.log("closeAlgolia()");
        });

        //Force reload on cart page when product is added from widget.
        $(document).ready(function () {
            if (!$("body").hasClass("checkout-cart-index")) {
                return;
            }

            // ✅ If we just arrived via a reload we triggered, stop here
            if (sessionStorage.getItem("cart_reload_triggered") === "1") {
                sessionStorage.removeItem("cart_reload_triggered");
                return;
            }

            var reloadTriggered = false;

            function triggerReload() {
                if (reloadTriggered) return;
                reloadTriggered = true;
                sessionStorage.setItem("cart_reload_triggered", "1");
                setTimeout(function () {
                    location.reload();
                }, 1500); // slight delay to let the cart AJAX complete first
            }

            // ✅ Target specifically: tocart-form whose action contains /checkout/cart/add/
            // This matches the PA widget forms but NOT the qty update form (#form-validate)
            $(document).on(
                "click",
                'form[data-role="tocart-form"][action*="/checkout/cart/add/"] button[type="submit"]',
                function () {
                    console.log("Widget add-to-cart clicked on cart page");
                    triggerReload();
                }
            );
        });
        
    });
});
