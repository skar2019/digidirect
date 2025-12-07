define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function (targetWidget) {
        $.widget('mage.catalogAddToCart', targetWidget, {
            disableAddToCartButton: function (form) {

                console.log('disableAddToCartButton called from mixin');

                var addToCartButton = $(form).find(this.options.addToCartButtonSelector),
                    buttonTextSpan = addToCartButton.find('span.addcart_words');

                addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
                addToCartButton.attr('title', 'Adding..');
                addToCartButton.prop('disabled', true);

                // Store original text before changing
                if (!addToCartButton.data('original-text')) {
                    addToCartButton.data('original-text', buttonTextSpan.html());
                }

                buttonTextSpan.html('Adding..');
            },

            enableAddToCartButton: function (form) {
                var addToCartButtonTextAdded = this.options.addToCartButtonTextAdded || $t('Added'),
                    self = this,
                    addToCartButton = $(form).find(this.options.addToCartButtonSelector),
                    buttonTextSpan = addToCartButton.find('span.addcart_words'),
                    originalButtonHtml = addToCartButton.data('original-text');

                // Temporarily change to "Added"
                buttonTextSpan.html(addToCartButtonTextAdded);
                addToCartButton.prop('title', addToCartButtonTextAdded);

                setTimeout(function () {
                    addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                    addToCartButton.prop('disabled', false); // Re-enable the button
                    // Restore the original HTML
                    buttonTextSpan.html(originalButtonHtml);
                    addToCartButton.prop('title', buttonTextSpan.text().trim());
                }, 1000);
            }
        });

        return $.mage.catalogAddToCart;
    };
});
