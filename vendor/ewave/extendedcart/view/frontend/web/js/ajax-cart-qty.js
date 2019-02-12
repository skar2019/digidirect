define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Checkout/js/model/checkout-data-resolver',
    'Magento_Checkout/js/model/cart/cache',
    'jquery/ui',
    'loader',
    'mage/mage'
], function ($, totals, checkoutData, cartCache) {
    'use strict';

    $.widget('ewave.ajaxCartQty', {
        options: {
            form: '#form-validate',
            inputQty: '.input-text.qty',
            clearCartButton: '[data-cart-empty]',
            loader: '.cart-container',
            loaderConfig: {}
        },

        isInProgress: false,

        _create: function () {
            this._bind();
            this._initLoader();
        },

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            this.element.on('change', this.options.inputQty, $.proxy(function () {     
                if ($(this.options.form).validation('isValid')) {
                    this.sendData();
                }
            }, this));

            this.element.on('submit', this.options.form, $.proxy(function (e) {     
                if (this.isInProgress) {
                    e.preventDefault();
                }
            }, this));
        },

        /**
         * Init loader
         * @private
         */
        _initLoader: function () {
            this.loader = $(this.options.loader).loader(this.options.loaderConfig);
        },

        /**
         * Send data
         */
        sendData: function () {
            var self = this,
                form = $(this.options.form),
                url = form.attr('action'),
                data = form.serialize();

            this.disableFields();
            this.isInProgress = true;
            this.loader.trigger('processStart');

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                cache: false
            }).done(function (res) {
                if (res.reload) {
                    window.location.reload();
                } else {
                    self._changeContent(res.blocks);
                    self.updateTotals();
                }
            }).fail(function (error) {
                console.warn(error);
            }).always(function () {
                self.loader.trigger('processStop');
                self.enableFields();
            });
        },

        /**
         * Change content
         * @param {object} data
         * @private
         */
        _changeContent: function (data) {
            var block;
            for (var key in data) {
                block = $(key);
                block.replaceWith(data[key]);
            }
            // trigger 'contentUpdated' doesn't work for magento cloud
            $.mage.init();
        },

        /**
         * Update totals
         */
        updateTotals: function () {
            cartCache.clear('cartVersion');
            checkoutData.resolveEstimationAddress();
            totals([]);
            this.isInProgress = false;
        },

        /**
         * Disable Qty fields
         */
        disableFields: function () {
            $(this.options.form).find(this.options.inputQty).attr('disabled', true);
        },

        /**
         * Enable Qty fileds
         */
        enableFields: function () {
            $(this.options.form).find(this.options.inputQty).attr('disabled', null);
        }
    });
    
    return $.ewave.ajaxCartQty;
});
