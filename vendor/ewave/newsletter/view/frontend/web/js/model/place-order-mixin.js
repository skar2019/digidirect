define([
    'jquery',
    'mage/utils/wrapper',
    'Ewave_Newsletter/js/model/newsletters-assigner'
], function ($, wrapper, newslettersAssigner) {
    'use strict';

    return function (placeOrderAction) {
        /** Override default place order action and add newsletter_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            newslettersAssigner(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});
