define([
    'jquery',
    'mage/utils/wrapper',
    'Ewave_Newsletter/js/model/newsletters-assigner'
], function ($, wrapper, newslettersAssigner) {
    'use strict';

    return function (placeOrderAction) {
        /** Override place-order-mixin for set-payment-information action as they differs only by method signature */
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            newslettersAssigner(paymentData);

            return originalAction(messageContainer, paymentData);
        });
    };
});
