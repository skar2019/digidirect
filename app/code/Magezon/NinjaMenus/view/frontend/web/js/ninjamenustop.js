define(["jquery", "./ninjamenus"], function ($) {
    "use strict";

    $.widget("mgz.ninjamenustop", $.mgz.ninjamenus, {
        _init: function () {
            var self = this;

            this._super();
            this._assignControls()._listen();
        },

        initListeners: function () {
            this._super();

            this.menu.mouseleave(() => {
                this.removeStaticClass();
            });
        },

        /**
         * @return {Object}
         * @private
         */
        _assignControls: function () {
            this.controls = {
                toggleBtn: $('[data-action="toggle-nav"]'),
                swipeArea: $(".nav-sections"),
            };
            return this;
        },

        /**
         * @private
         */
        _listen: function () {
            var controls = this.controls,
                toggle = this.toggle;
            if (!controls.toggleBtn.hasClass("ninjamenus-top-triggered")) {
                this._on(controls.toggleBtn, {
                    click: toggle,
                });
                this._on(controls.swipeArea, {
                    swipeleft: toggle,
                });
                controls.toggleBtn.addClass("ninjamenus-top-triggered");
            }
        },

        /**
         * Toggle.
         */
        toggle: function () {
            var html = $("html");
            if (html.hasClass("nav-open")) {
                html.removeClass("nav-open");
                setTimeout(function () {
                    html.removeClass("nav-before-open");
                }, this.options.hideDelay);
            } else {
                html.addClass("nav-before-open");
                setTimeout(function () {
                    html.addClass("nav-open");
                }, this.options.showDelay);
            }
        },

        initStickMenu: function () {
            if (this.element.parents(".nav-sections")) {
                this._initScrollToFixed(this.element.parents(".nav-sections"));
            }
        },

        onMouseHover: function (item) {
            this._super(item);
            clearTimeout(this.mouseLeaveTimeout);
            if (this.isDesktop) {
                if (item.hasClass("nav-item-static")) {
                    item.closest(".magezon-builder").addClass(
                        "nav-item-static"
                    );
                    item.closest(".ninjamenus").addClass("nav-item-static");
                    item.closest(".navigation").addClass("nav-item-static");
                } else {
                    this.removeStaticClass();
                }
            }
        },

        removeStaticClass: function () {
            $(this.element)
                .find(".magezon-builder")
                .removeClass("nav-item-static");

            $(this.element).removeClass("nav-item-static");
            $(this.element)
                .closest(".navigation")
                .removeClass("nav-item-static");
        },
    });

    return $.mgz.ninjamenustop;
});
