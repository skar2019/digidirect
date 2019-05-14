define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'Magento_Checkout/js/model/cart/estimate-service'
], function (ko, Component, quote, $, estimate) {
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

            applyDeliveryToAllItems: function () {
                this._super();

                this.toggleToDeliveryShippingMethod();
            },

            toggleToDeliveryShippingMethod: function () {
                var closestRadioButton = $('.row.collect').siblings().find('input')[0];
                if(closestRadioButton) {
                    $(closestRadioButton).trigger('click');
                }
            }
        });
    }
});
