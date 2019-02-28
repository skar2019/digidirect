define([
    'jquery',
    'underscore',
    'jquery/ui',
    'mage/translate',
    'customSelectInit'
], function ($, _) {
    'use strict';

    return function (target) {
        $.widget('mage.SwatchRenderer', target, {

            _init: function () {
                this._initSelectCustomInited();

                if (this.element.html() === '') {
                    this._super();
                }

               this._Rebuild();

                $('select').on('selectric-before-open', function(event, element, selectric) {
                    selectric.refresh();
                });
            },

            /**
             * Init Custom Select
             */
            _initSelectCustomInited: function () {
                $(this.element).on('swatch.initialized', $.proxy(function () {
                    this.swatchSelect = $(this.element).find('.swatch-select');
                    this.swatchSelect.customSelect({responsive: true});
                }, this));

            }
        });

        return $.mage.SwatchRenderer;
    };
});
