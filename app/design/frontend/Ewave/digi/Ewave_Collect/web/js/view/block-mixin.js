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

                var deliveryChecked =  $('[data-collect-type="delivery"]').hasClass(this.visibleClass);

                this.toggleToDeliveryShippingMethod();

                quote.shippingMethod.subscribe(function () {
                    if (quote.shippingMethod()) {
                        if (quote.shippingMethod().carrier_code === 'collect' && deliveryChecked) {
                            this.toggleToDeliveryShippingMethod();
                        }
                    }
                },this);
            },

            toggleToDeliveryShippingMethod: function () {
                $('.row.collect').siblings().find('input').trigger('click');
            }
        });
    }
});
