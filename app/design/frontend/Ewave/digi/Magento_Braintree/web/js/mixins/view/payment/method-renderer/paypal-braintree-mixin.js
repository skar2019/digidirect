define([
    'Magento_Checkout/js/model/quote',
    'underscore'
], function (quote, _) {
    'use strict';

    var mixin = {
        getShippingAddress: function () {
            var address = quote.shippingAddress();
            if (_.isNull(address)) {
                return {};
            }
            if (!address.street) {
                address.street = ['', '', ''];
            }

            return {
                recipientName: address.firstname + ' ' + address.lastname,
                line1: address.street[0],
                line2: typeof address.street[2] === 'undefined' ? address.street[1] : address.street[1] + ' ' + address.street[2],
                city: address.city,
                countryCode: address.countryId,
                postalCode: address.postcode,
                state: address.region
            };
        }
    };
    return function (target) {
        return target.extend(mixin);
    };
});
