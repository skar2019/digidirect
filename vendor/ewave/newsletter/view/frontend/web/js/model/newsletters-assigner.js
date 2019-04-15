define([
    'jquery'
], function ($) {
    'use strict';

    var newslettersConfig = window.checkoutConfig.checkoutNewsletterSubscribe;

    /** Override default place order action and add newsletter_ids to request */
    return function (paymentData) {
        var newsletterForm,
            newsletterData,
            newsletterIds;

        if (!newslettersConfig.isEnabled) {
            return;
        }

        newsletterForm = $('.payment-method._active [data-role="checkout-newsletters"] input');
        newsletterData = newsletterForm.serializeArray();
        newsletterIds = [];

        newsletterData.forEach(function (item) {
            newsletterIds.push(item.value);
        });

        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        paymentData['extension_attributes']['newsletter_ids'] = newsletterIds;
    };
});
