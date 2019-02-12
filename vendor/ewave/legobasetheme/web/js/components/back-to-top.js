define([
    'jquery',
    'underscore',
    'jquery/ui'
], function ($, _) {
    'use strict';

    $.widget('ewave.backTop', {
        options: {
            scrollStartClass: '-scroll',
            scrollUpStateClass: '-up',
            scrollDownStateClass: '-down',
            scrollIndent: 100,
            alwaysVisible: false,
            scrollBodyTo: 0
        },

        _create: function () {
            this.lastScrollTop = 0;

            if (!this.options.alwaysVisible) {
                window.addEventListener('scroll', this._scroll.bind(this));
            }

            this._attachEvents();
        },

        _scroll: _.debounce(function () {
            var bodyScroll = $(window).scrollTop();
            // if scrolled less than min settings
            if (bodyScroll < this.options.scrollIndent) {
                // remove all state classes
                this.element.removeClass(this.options.scrollStartClass + ' ' + this.options.scrollUpStateClass + ' ' + this.options.scrollDownStateClass);
            } else {
                // if scrolled more than min settings
                // add scroll start class
                this.element.addClass(this.options.scrollStartClass);
                if (bodyScroll > this.lastScrollTop) {
                    // scroll down
                    this.element.addClass(this.options.scrollDownStateClass).removeClass(this.options.scrollUpStateClass);
                } else {
                    // scroll up
                    this.element.addClass(this.options.scrollUpStateClass).removeClass(this.options.scrollDownStateClass);
                }
            }
            this.lastScrollTop = bodyScroll;
        }, 30),

        _attachEvents: function () {
            var scrollTo = this.options.scrollBodyTo,
                href = this.element.attr('href'),
                anchorId,
                anchorElement;
            // check if href is availiable
            if (href) {
                anchorId = this.element.attr('href').replace(/#/g, '');
                anchorElement = $('#' + anchorId);
            }
            this.element.on('click', function (e) {
                e.preventDefault();
                // if element has href with id
                if (anchorId && (anchorElement.length)) {
                    scrollTo = anchorElement.offset().top;
                }
                $('html,body').animate({
                    scrollTop: scrollTo
                });
            });
        }
    });

    return $.ewave.backTop;
});
