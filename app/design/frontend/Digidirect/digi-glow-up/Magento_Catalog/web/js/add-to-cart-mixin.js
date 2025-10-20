define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (targetWidget) {
        $.widget('mage.catalogAddToCart', targetWidget, {
            enableAddToCartButton: function (form) {
                var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added'),
                    self = this,
                    addToCartButton = $(form).find(this.options.addToCartButtonSelector),
                    originalButtonText = addToCartButton.find('span').text(); // Store original text

                addToCartButton.find('span').text(addToCartButtonTextAdded);
                addToCartButton.prop('title', addToCartButtonTextAdded);

                setTimeout(function () {
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.find('span').text(originalButtonText); // Use original text
                    addToCartButton.prop('title', originalButtonText);
                }, 1000);
            }
        });

        return $.mage.catalogAddToCart;
    };
});
