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
                    buttonTextSpan = addToCartButton.find('span.addcart_words'),
                    originalButtonHtml = buttonTextSpan.html(); // Store the entire HTML content

                // Temporarily change to "Added"
                buttonTextSpan.html(addToCartButtonTextAdded);
                addToCartButton.prop('title', addToCartButtonTextAdded);

                setTimeout(function () {
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    // Restore the original HTML (preserves "Pre-Order", "Special Order", or "Add to Cart" with tooltip)
                    buttonTextSpan.html(originalButtonHtml);
                    addToCartButton.prop('title', buttonTextSpan.text().trim());
                }, 1000);
            }
        });

        return $.mage.catalogAddToCart;
    };
});
