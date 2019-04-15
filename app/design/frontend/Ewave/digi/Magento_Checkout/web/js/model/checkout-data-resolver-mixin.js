define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method'
], function (
    $,
    quote,
    checkoutData,
    selectShippingMethodAction
) {
    'use strict';
    return function (target) {
        target.resolveShippingRates = function (ratesData) {
            var selectedShippingRate = checkoutData.getSelectedShippingRate(),
                availableRate = false,
                freeshippingExist = ratesData.filter(function(item) {
                    return item.method_code == 'freeshipping';
                }),
                flaterateExist = ratesData.filter(function(item) {
                    return item.method_code == 'flatrate';
                });

            if (ratesData.length === 1) {
                //set shipping rate if we have only one available shipping rate
                selectShippingMethodAction(ratesData[0]);

                return;
            }

            if (quote.shippingMethod()) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] == quote.shippingMethod()['carrier_code'] && //eslint-disable-line
                        rate['method_code'] == quote.shippingMethod()['method_code']; //eslint-disable-line eqeqeq
                });
            }

            if (!availableRate && selectedShippingRate) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] + '_' + rate['method_code'] === selectedShippingRate;
                });
            }

            if (!availableRate && window.checkoutConfig.selectedShippingMethod) {
                availableRate = window.checkoutConfig.selectedShippingMethod;
                selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);

                return;
            }

            //Unset selected shipping method if not available
            if (!availableRate) {
                selectShippingMethodAction(null);
            } else {
                selectShippingMethodAction(availableRate);
            }

            if (freeshippingExist.length > 0 && !availableRate) {
                selectShippingMethodAction(freeshippingExist[0]);
            } else if(flaterateExist.length > 0 &&!availableRate) {
                selectShippingMethodAction(flaterateExist[0]);
            }

        };
        return target;
    };
});
