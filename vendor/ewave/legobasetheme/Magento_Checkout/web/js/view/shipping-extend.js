define([
    'Magento_Checkout/js/view/shipping',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer'
], function (target, ko, quote, customer) {
    'use strict';

    return target.extend({
        defaults: {
            isShippingVisible: ko.observable(!(customer.isLoggedIn() && quote.customShipping || customer.isLoggedIn() && quote.isShippingAddressHidden)),
            isDefaultShipping: !quote.customShipping,
            isShippingAddressVisible: ko.observable(!quote.isShippingAddressHidden),
            isSaveShippingInAddressBook: ko.observable(true),
            titleClasses: ko.observable(''),
            methodListClasses: ko.observable('')
        }
    });
});
