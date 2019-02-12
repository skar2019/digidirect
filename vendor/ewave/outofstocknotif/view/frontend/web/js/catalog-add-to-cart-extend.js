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
                if (this.element.find(this.options.addToCartButtonSelector).hasClass(this.options.backorderButtonClassname)) {
                    this.options.addToCartButtonTextDefault = this.options.backorderButtonLabel;
                }
                this._super();
            }
        });

        return $.mage.catalogAddToCart;
    };
});
