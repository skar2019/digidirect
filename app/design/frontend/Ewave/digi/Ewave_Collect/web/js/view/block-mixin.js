define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'jquery'
], function (ko, Component, quote, $) {
    'use strict';

    return function (target) {
        return target.extend({
            onSuccessApplyPlace: function (response) {
                this._super(response);
                window.selectStore(false);
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
