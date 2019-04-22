define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/full-screen-loader',
    'Ewave_Newsletter/js/model/newsletters-assigner'
], function ($, quote, urlBuilder, storage, errorProcessor, customer, fullScreenLoader, newslettersAssigner) {
    'use strict';

    return function (target) {
        return function (messageContainer) {
            var serviceUrl,
                payload,
                method = 'put',
                paymentData = quote.paymentMethod();

            newslettersAssigner(paymentData);

            /**
             * Checkout for guest and registered customer.
             */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:cartId/set-payment-information', {
                    cartId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    email: quote.guestEmail,
                    paymentMethod: paymentData
                };
                method = 'post';
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/selected-payment-method', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    method: paymentData
                };
            }
            fullScreenLoader.startLoader();

            return storage[method](
                serviceUrl, JSON.stringify(payload)
            ).fail(function (response) {
                errorProcessor.process(response, messageContainer);
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        };
    };
});
