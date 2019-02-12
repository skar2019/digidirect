/**
 * Initialize slider
 */
define([
    "jquery",
    "slickCarousel",
    "domReady!"
], function ($) {
    "use strict";

    $.widget('ewave.slickInit', {
        options: {
            mobileFirst: true,
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
        _init: function () {
            var sliderSelector = this.element;
            if (sliderSelector) {
                $(sliderSelector).slick(this.options);
            }
        }
    });

    return $.ewave.slickInit;
});