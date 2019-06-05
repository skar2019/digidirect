define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Catalog/js/catalog-add-to-cart',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.outstockNotification', target, {
            options: {
                preorderButtonClassname: '-pre-order'
            },

            setBackOrderAddToCart: function () {
                if (this.options.notificationForBackorder && this.options.isBackorderProduct &&
                    !this.element.find(this.options.addToCartButtonSelector).hasClass(this.options.preorderButtonClassname)) {
                    var $addToCartButton = $(this.options.addToCartFormSelector).find(this.options.addToCartButtonSelector);
                    $addToCartButton.find('span').text(this.options.backorderButtonLabel);
                    $addToCartButton.attr('title', this.options.backorderButtonLabel);
                    $addToCartButton.addClass(this.options.backorderButtonClassname);
                }
            }
        });

        return $.ewave.outstockNotification;
    }
});
