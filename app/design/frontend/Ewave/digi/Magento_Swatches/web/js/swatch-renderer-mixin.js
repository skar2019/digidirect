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
                    this.swatchSelect.customSelect();

                    // Adding selected attribute for original select

                    this.swatchSelect.closest('.swatch-attribute').attr('option-selected',this.swatchSelect.val());
                }, this));

            },

            /**
             * Render select by part of config
             *
             * @param {Object} config
             * @param {String} chooseText
             * @returns {String}
             * @private
             */
            _RenderSwatchSelect: function (config, chooseText) {
                var html;

                if (this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                    return '';
                }

                html =
                    '<select class="' + this.options.classes.selectClass + ' ' + config.code + '">';

                $.each(config.options, function () {
                    var label = this.label,
                        attr = ' value="' + this.id + '" option-id="' + this.id + '"';

                    if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                        attr += ' option-empty="true"';
                    }

                    html += '<option ' + attr + '>' + label + '</option>';
                });

                html += '</select>';

                return html;
            }
        });

        return $.mage.SwatchRenderer;
    };
});
