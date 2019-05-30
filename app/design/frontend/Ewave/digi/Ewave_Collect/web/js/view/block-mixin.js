define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'Magento_Checkout/js/model/cart/estimate-service',
    'Magento_Ui/js/lib/view/utils/async'
], function (ko, Component, quote, $, estimate, async) {
    'use strict';

    return function (target) {
        return target.extend({
            onSuccessApplyPlace: function (response) {
                this._super(response);
                var pageTypeIsCheckout = $('body').hasClass('checkout-index-index');
                if (pageTypeIsCheckout) {
                    window.selectStore(false);
                }
            },
            onSuccessDelivery: function () {
                this._super();
                this.toggleToDeliveryShippingMethod();
            },
            toggleToDeliveryShippingMethod: function () {
                var closestRadioButtons = $('.row.collect').siblings().find('input');

                if(!closestRadioButtons.filter(':checked').length) {
                    closestRadioButtons.first().trigger('click');
                }
            },

            selectCollectShippingMethod: function () {
                async.async('div[data-collect-type="delivery"]', function (node) {
                    $(node).removeClass('-visible');
                });

                async.async('div[data-collect-type="collect"]', function (node) {
                    $(node).addClass('-visible');
                });
            }
        });
    }
});
