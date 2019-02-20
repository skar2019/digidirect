define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'slickInit'
], function ($, $t) {
    'use strict';

    $.widget('ewave.finderSlider', {
        options: {
            finderSlider: '.finder-slider',
            finderSliderBack: '[data-role="to-category"]'
        },
        _create: function () {
            this.initSlier();
            this.bind();
        },
        initSlier: function() {
            this.element.slickInit({
                infinite: false,
                adaptiveHeight: true,
                nextArrow: '<button type="button" class="button next -responsive" title="' + $t('Continue') + '" tabindex="0">' + $t('Continue') + '</button>',
                draggable: false,
                swipe: false,
                dots: true,
                appendArrows: this.element.closest(this.options.finderSlider).find('.actions-toolbar')
            });
        },
        bind: function () {
            var self = this;

            this.element.closest(this.options.finderSlider).find(this.options.finderSliderBack).on('click', function (e) {
                e.preventDefault();

                self.element.slick('slickGoTo', 0);
            });

            this.element.closest(this.options.finderSlider).on('click', '[data-role="prev-slide"]', function () {
                $(this).closest(self.options.finderSlider).find(self.options.finderSliderBack).trigger('click');
            });
        }
    });

    return $.ewave.finderSlider;
});