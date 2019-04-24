define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/cart/estimate-service'
], function ($, ko, quote, addressList, estimate) {
    'use strict';
    
    var mixin = {
        initialize: function () {
            window.selectStore = ko.observable(false);
            this._super();
            var self = this;

            quote.shippingMethod.subscribe(function (shippingMethod) {
                if (self.rates().length == 1) {
                    $('#s_method_' + shippingMethod.method_code).closest('.row').addClass('-active').siblings().removeClass('-active');
                } else {
                    $('#s_method_' + shippingMethod.carrier_code + '_' + shippingMethod.method_code).closest('.row').addClass('-active').siblings().removeClass('-active');
                }
            });

            return this;
        },
        saveNewAddress: function () {
            this._super();
            if (!this.source.get('params.invalid')) {
                $('.edit-address-link').show();
            }
        },

        onErrorValidationShippingInformation: function (type) {
            var pageTypeIsCheckout = $('body').hasClass('checkout-index-index');
            if (pageTypeIsCheckout) {
                window.selectStore(true);
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});