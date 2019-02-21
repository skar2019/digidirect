define([
    'jquery',
    'mage/translate',
    'mage/template',
    'jquery/ui',
    'slickInit'
], function ($, $t, mageTemplate) {
    'use strict';

    $.widget('ewave.finderSlider', {
        options: {
            finderSlider: '.finder-slider',
            finderSliderActive: '-active',
            finderSliderBack: '[data-role="to-category"]',
            categoryVisibility: 'no-display',
            nextStepArrowTitle: 'Continue',
            penultimateClassName: '-penultimate',
            lastStepClassName: '-last-step',
            resultsCount: '[data-template="finder-results"]',
            noResultsTemplate: '[data-template="finder-no-results"]'
        },
        _create: function () {
            this.initSlider();
            this.bind();
        },
        initSlider: function() {
            this.element.slickInit({
                infinite: false,
                adaptiveHeight: true,
                nextArrow: '<button type="button" class="button next -responsive -fixed-sizes" title="' + $t(this.options.nextStepArrowTitle) + '" tabindex="0">' + $t(this.options.nextStepArrowTitle) + '</button>',
                draggable: false,
                swipe: false,
                dots: true,
                appendArrows: this.element.closest(this.options.finderSlider).find('.actions-toolbar')
            });
        },
        bind: function () {
            this.resetFinder();
            this.onSliderEvents();
            this.goToStart();
            this.onRequestResults();
        },
        resetFinder: function () {
            var self = this;

            this.element.closest(this.options.finderSlider).find(this.options.finderSliderBack).on('click', function (e) {
                e.preventDefault();
                var $this = $(this),
                    $categories = $($this.attr('href')),
                    $slider = $this.closest(self.options.finderSlider);

                if ($slider.attr('data-prev-step') !== undefined && $this.data('action') === 'back') {
                    self.element.slick('slickGoTo', $slider.attr('data-prev-step'));
                } else {
                    $categories.find('input:radio').prop('checked', false);
                    $slider.removeClass(self.options.finderSliderActive).find('form')[0].reset();
                    $categories.removeClass(self.options.categoryVisibility);

                    self.element.slick('slickGoTo', 0);
                }

                self.clearTemplates();
            });
        },
        onSliderEvents: function () {
            var self = this;

            this.element.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
                if (slick.slideCount > 1 && (slick.slideCount - (nextSlide + 1) === 1)) {
                    self.element.closest(self.options.finderSlider).addClass(self.options.penultimateClassName);
                } else {
                    self.element.closest(self.options.finderSlider).removeClass(self.options.penultimateClassName);
                }

                // If last step
                if (slick.slideCount === (nextSlide + 1)) {
                    self.element.closest(self.options.finderSlider).addClass(self.options.lastStepClassName).attr('data-prev-step', currentSlide);
                } else {
                    self.element.closest(self.options.finderSlider).removeClass(self.options.lastStepClassName).removeAttr('data-prev-step');
                }
            });
        },
        goToStart: function () {
            var self = this;

            this.element.closest(this.options.finderSlider).find('[data-role="to-start"]').on('click', function (e) {
                self.element.slick('slickGoTo', 0);
                self.clearTemplates();
            });
        },
        onRequestResults: function () {
            var self = this;

            $(document).on('finder.results.error', function (e, $container) {
                var $slider = $container.prev(self.options.finderSlider).find('.slick-slider');
                self.renderNoResults($slider);
                self.goToFinish($slider);
            });

            $(document).on('finder.results.success', function (e, $container, data) {
                var $slider = $container.prev(self.options.finderSlider).find('.slick-slider'),
                    count;

                if (data.error) {
                    self.renderNoResults($slider);
                } else {
                    count = $(data).find('ol.products > li').length || 0;
                    self.renderResults(count);
                }
                self.goToFinish($slider);
            });
        },
        goToFinish: function ($slider) {
            $slider.slick('slickGoTo', $slider.slick('getSlick').slideCount - 1);
        },
        renderNoResults: function ($slider) {
            var $template = this.element.closest(this.options.finderSlider).find(this.options.noResultsTemplate);

            if ($template.length) {
                var $stepSlides = $slider.slick('getSlick').$slides;

                $($stepSlides[$stepSlides.length - 1]).find('.field-group-category').prepend(mageTemplate($.trim($template.html())));
            }
            this.renderResults(0);
        },
        renderResults: function (count) {
            var $template = this.element.closest(this.options.finderSlider).find(this.options.resultsCount);

            if ($template.length) {
                this.element.closest(this.options.finderSlider).find('.actions-toolbar').prepend(mageTemplate($.trim($template.html()), {
                    data: {
                        _count_: count
                    }
                }));
            }
        },
        clearTemplates: function () {
            var $slider = this.element.closest(this.options.finderSlider);
            $slider.find('[data-role="finder-count"], [data-role="finder-no-results"]').remove();
            $slider.next('.calculator-result').empty();
        }
    });

    return $.ewave.finderSlider;
});