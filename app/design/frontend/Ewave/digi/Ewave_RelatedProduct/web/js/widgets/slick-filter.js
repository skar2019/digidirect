define([
    'underscore',
    "jquery",
    'jquery/ui',
    "slickCarousel",
    "domReady!"
], function (_, $) {
    "use strict";

    $.widget('ewave.slickFilterInit', {
        options: {
            infinite: false,
            mobileFirst: true,
            slidesToShow: 2,
            slidesToScroll: 2,
            prevArrow: '' +
                '<div class="slick-prev" aria-label="Previous" tabindex="0" role="button">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" class="svg-icon svg-icon-arrow-left">' +
                        '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svgi-left"></use>' +
                    '</svg>' +
                '</div>',
            nextArrow: '' +
                '<div class="slick-next" aria-label="Next" tabindex="0" role="button">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" class="svg-icon svg-icon-arrow-right">' +
                        '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#svgi-right"></use>' +
                    '</svg>' +
                '</div>'
        },
        breakpoints: {
            screen_m: '767',
            screen_l: '1023',
            screen_xl: '1439'
        },
        activeFilter: '.related-filters > .item.-active',

        _init: function () {    
            this._setOptions();
            this._initSlider();
            $(window).resize(_.debounce(this._reinitSlider.bind(this), 300));
            $(this.element).on('filter:start', this._unfilter.bind(this));
            $(this.element).on('filter:done', this._filter.bind(this));           
        },

        _setOptions: function() {
            var currentWidth = $(window).width();

            //Mobile
            if (currentWidth < this.breakpoints.screen_m) {
                this.options.slidesToShow = 2;
            } 

            //Tablet
            if (currentWidth > this.breakpoints.screen_m && currentWidth < this.breakpoints.screen_l) {
                this.options.slidesToShow = 3;
            }

            //Descktop
            if (currentWidth > this.breakpoints.screen_l && currentWidth < this.breakpoints.screen_xl) {
                this.options.slidesToShow = 4;
            }

            //Large desktop
            if (currentWidth > this.breakpoints.screen_xl) {
                this.options.slidesToShow = 5;
            }
        },

        _initSlider: function() {
            $(this.element).slick(this.options);
        },

        _destroySlider: function() {
            $(this.element).slick('unslick');
        },

        _reinitSlider: function() {
            this._unfilter();
            this._setOptions();
            this._destroySlider();
            this._initSlider();
            this._triggerFilter();
        },

        _triggerFilter: function() {
            $(this.activeFilter).removeClass('-active').trigger('click');
        },

        _filter: function() {
            $(this.element).slick('slickFilter', '[class*=-visible]');
        },

        _unfilter: function () {
            $(this.element).slick('slickUnfilter');
        }
    });

    return $.ewave.slickFilterInit;
});