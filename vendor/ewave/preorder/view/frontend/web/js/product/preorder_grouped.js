define([
    'jquery',
    'jquery/ui',
    'preOrder'
], function ($) {
    'use strict';

    $.widget('ewave.preOrderGrouped', $.ewave.preOrder, {
        options: {
            map: {},
            preOrderNoteTemplate: ''
        },

        _create: function () {
            this._super();
            this.bind();
        },

        bind: function () {
            var self = this;
            $.each(this.options.map, function (key, value) {
                $('.qty input[name=super_group\\[' + key + '\\]]').on('change', function () {
                    if (this.value > 0) {
                        self.options.addToCartLabel = value.cartLabel;
                        self.options.preOrderNote = value.note;
                        self.enable();
                    } else {
                        self.disable();
                    }
                });
                $('.grouped .price-box.price-final_price[data-product-id=' + key + ']').append(self.options.preOrderNoteTemplate.replace('{preorderNote}', value.note));
            });
        },

        _changeLabels: function () {
            $.mage.catalogAddToCart.prototype.options.addToCartButtonTextDefault = this.options.addToCartLabel;
            this._setButtonLabel(this.options.addToCartLabel);
        }
    });

    return $.ewave.preOrderGrouped;
});
