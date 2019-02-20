define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('ewave.finderCategorySwitcher', {
        options: {
            finderSlider: '.finder-slider',
            finderSliderActive: '-active',
            finderSliderBack: '[data-role="to-category"]',
            categoryVisibility: 'no-display',
            inputPrefix: 'calculator-',
            inputGroups: ''
        },
        _create: function () {
            var $active = this.element.find('input[type="radio"]:checked');

            this.bind();

            if ($active.length) {
                this.setActiveFinder(this.element.find('input[type="radio"]').index($active), $active);
            } else {
                this.setActiveFinderByRequestParams(this.options.inputGroups);
            }
        },
        bind: function () {
            var self = this,
                $finderSlider = $(self.options.finderSlider);

            this.element.find('input:radio').on('click', function () {
                var index = self.element.find('input:radio').index(this);
                self.setActiveFinder(index, $(this));
            });

            $finderSlider.find(this.options.finderSliderBack).on('click', function (e) {
                e.preventDefault();

                var $this = $(this);

                self.element.find('input:radio').prop('checked', false);
                $this.closest(self.options.finderSlider).removeClass(self.options.finderSliderActive).find('form')[0].reset();
                $($this.attr('href')).removeClass(self.options.categoryVisibility);
            });
        },
        setActiveFinder: function (index, $element) {
            var $finderSlider = $(this.options.finderSlider);

            if ($element.prop('checked')) {
                this.element.addClass(this.options.categoryVisibility);
            }
            $finderSlider.removeClass(this.options.finderSliderActive);
            if ($finderSlider.eq(index).length) {
                $finderSlider.eq(index).addClass(this.options.finderSliderActive);
            } else {
                this.element.removeClass(this.options.categoryVisibility);
            }
        },
        setActiveFinderByRequestParams: function (inputGroups) {
            if (inputGroups && typeof inputGroups === 'object') {
                var self = this;
                $.each(inputGroups, function(i, val) {
                    $('#' + self.options.inputPrefix + i + '-' + val[0]).prop('checked', true).trigger('click');
                });
            }
        }
    });

    return $.ewave.finderCategorySwitcher;
});
