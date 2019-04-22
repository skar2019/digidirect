define([
    'jquery',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/model/address-list',
    'uiRegistry'
], function ($, customer, addressList, uiRegistry) {
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
            }
        });
    };
});
