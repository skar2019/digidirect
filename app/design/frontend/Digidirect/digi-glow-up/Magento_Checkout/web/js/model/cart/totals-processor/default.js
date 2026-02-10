/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/resource-url-manager',
    'mage/storage',
    'Magento_Checkout/js/model/cart/cache',
    'underscore'
], function (customerData, quote, totalsService, resourceUrlManager, storage, cartCache, _) {
    'use strict';

    return {
        estimateTotals: function (address) {
            var serviceUrl,
                payload,
                shippingMethod;

            // Start loader for totals block
            totalsService.isLoading(true);
            serviceUrl = resourceUrlManager.getUrlForTotalsEstimationForNewAddress(quote);
            payload = {
                addressInformation: {
                    address: _.pick(address, cartCache.requiredFields)
                }
            };

            shippingMethod = quote.shippingMethod();

            // Only include shipping method if it exists AND has valid codes
            // This prevents sending shipping codes when switching to Delivery mode
            if (shippingMethod &&
                shippingMethod.method_code &&
                shippingMethod.carrier_code) {
                payload.addressInformation.shipping_method_code = shippingMethod.method_code;
                payload.addressInformation.shipping_carrier_code = shippingMethod.carrier_code;
            }

            return storage.post(
                serviceUrl, JSON.stringify(payload), false
            ).done(function (result) {
                var data = {
                    totals: result,
                    address: address,
                    cartVersion: customerData.get('cart')()['data_id'],
                    shippingMethodCode: null,
                    shippingCarrierCode: null
                };

                if (quote.shippingMethod() && quote.shippingMethod()['method_code']) {
                    data.shippingMethodCode = quote.shippingMethod()['method_code'];
                    data.shippingCarrierCode = quote.shippingMethod()['carrier_code'];
                }
                cartCache.set('cart-data', data);
                quote.setTotals(result);
            }).fail(function () {
                quote.setTotals({});
            }).always(function () {
                quote.isLoading(false);
            });
        }
    };
});
