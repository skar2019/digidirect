define([
    'jquery',
    'mage/translate',
    'jquery/ui'
], function ($, $t) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            options: {
                backorderButtonLabel: $t('Back-order'),
                backorderButtonClassname: '-back-order'
            },
            _create: function () {
                this.setBackOrderButtonText();
                this._super();
            },
            setBackOrderButtonText: function () {
                if (!this.options.addToCartButtonTextDefault && this.element.find(this.options.addToCartButtonSelector).hasClass(this.options.backorderButtonClassname)) {
                    this.options.addToCartButtonTextDefault = this.options.backorderButtonLabel;
                }
            },
            enableAddToCartButton: function (form) {
                this._super(form);
            }
        });

        return $.mage.catalogAddToCart;
    };
});
