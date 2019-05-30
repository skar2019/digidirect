define([
    'jquery',
    'underscore',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-address',
    'uiRegistry'
], function ($, _, customer, addressList, addressConverter, quote, selectShippingAddress, uiRegistry) {
    'use strict';

    var isSingleCartCollectVariation = window.checkoutConfig.quoteData.is_single_cart_collect_variation;

    return function (target) {
        return target.extend({
            defaults: {
                collectBlockUiRegistryName: 'checkout.steps.shipping-step.shippingAddress.collect_block'
            },
            initialize: function () {
                this._super();

                this.hideAddressFormWhatever();
                this.bindAddressFormWhatever();

                return this;
            },

            hideAddressFormWhatever: function () {
                if (this.isEnableAddressFormWhatever()) {
                    this.addressFormWhatever(false);
                    this.isSaveShippingInAddressBook(true);
                }
            },

            bindAddressFormWhatever: function () {
                var self = this;
                if (this.isEnableAddressFormWhatever()) {
                    $(document).on('change', 'input[name="delivery_type"]', function () {
                        if ($(this).val() === 'collect') {
                            self.addressFormWhatever(true);
                            self.isSaveShippingInAddressBook(false);
                        } else {
                            self.addressFormWhatever(false);
                            self.isSaveShippingInAddressBook(true);
                        }
                    });
                    uiRegistry.async(this.collectBlockUiRegistryName)(function (field) {
                        if (field && field.collectPlaces() && field.collectPlaces().length) {
                            self.addressFormWhatever(true);
                            self.isSaveShippingInAddressBook(false);
                        }
                    });
                }
            },

            isEnableAddressFormWhatever: function () {
                return customer.isLoggedIn() && isSingleCartCollectVariation && addressList().length > 0;
            },

            validateShippingInformation: function () {
                var result = this._super(),
                    shippingAddress,
                    addressData,
                    field;

                if (this.isEnableAddressFormWhatever() && $('input[name="delivery_type"]') === 'collect') {
                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    // Copy form data to quote shipping address object
                    for (field in addressData) {
                        if (addressData.hasOwnProperty(field) &&
                            shippingAddress.hasOwnProperty(field) &&
                            typeof addressData[field] != 'function' &&
                            _.isEqual(shippingAddress[field], addressData[field])
                        ) {
                            shippingAddress[field] = addressData[field];
                        } else if (typeof addressData[field] != 'function' &&
                            !_.isEqual(shippingAddress[field], addressData[field])) {
                            shippingAddress = addressData;
                            break;
                        }
                    }

                    shippingAddress['save_in_address_book'] = 1;
                    selectShippingAddress(shippingAddress);
                }
                return result;
            }
        });
    };
});
