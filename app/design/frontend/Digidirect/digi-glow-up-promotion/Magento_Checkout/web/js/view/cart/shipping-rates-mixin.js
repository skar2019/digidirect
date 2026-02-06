define([
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/action/get-totals',
    'jquery'
], function (quote, checkoutData, selectShippingMethodAction, getTotalsAction, $) {
    'use strict';

    return function (target) {
        return target.extend({
            selectShippingMethod: function (shippingMethod) {
                if (!shippingMethod) {
                    return false;
                }

                checkoutData.setSelectedShippingRate(
                    shippingMethod.carrier_code + '_' + shippingMethod.method_code
                );
                selectShippingMethodAction(shippingMethod);

                getTotalsAction([], $.Deferred()).always(function() {
                    $('input[name="delivery_type"]').prop('disabled', false);
                    $('body').trigger('processStop');
                });

                return true;
            },

            selectedShippingMethod: function() {
                var method = quote.shippingMethod();
                return method ? method.carrier_code + '_' + method.method_code : null;
            }
        });
    };
});
