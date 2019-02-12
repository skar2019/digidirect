define([
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Customer/js/model/address-list'
], function (
    formPopUpState,
    addressList
) {
    'use strict';
    return function (target) {
        return target.extend({
            shippingFields: (window.customCheckoutFieldConfig !== undefined) ? window.customCheckoutFieldConfig.fields['shipping-step'] : false,
            billingFields: (window.customCheckoutFieldConfig !== undefined) ? window.customCheckoutFieldConfig.fields['billing-step'] : false,
            isFormInline: addressList().length === 0,
            validate: function () {
                if (this.billingFields && this.isCustomBillingFields()) {
                    return this._super();
                }

                if (this.shippingFields && !this.isFormInline) {
                    if (formPopUpState.isVisible()) {
                        if (this.isCustomShippingField()) {
                            return false;
                        }
                    } else {
                        if (!this.isCustomShippingField()) {
                            return false;
                        }
                    }
                }
                return this._super();
            },
            isCustomShippingField: function () {
                if (this.shippingFields === undefined) {
                    return false;
                }
                return this.inputName in this.shippingFields;
            },
            isCustomBillingFields: function () {
                if (this.billingFields === undefined) {
                    return false;
                }
                return this.inputName in this.billingFields;
            },
            isCustomCheckoutField: function () {
                return this.isCustomShippingField() || this.isCustomBillingFields();
            }
        });
    };
});
