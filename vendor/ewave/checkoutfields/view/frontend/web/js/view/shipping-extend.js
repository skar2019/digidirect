define([
    'Magento_Checkout/js/model/quote',
    'Ewave_CheckoutFields/js/model/skip-state'
], function (
    quote,
    skipState
) {
    'use strict';
    return function (target) {
        return target.extend({
            initialize: function () {
                this._super();
                quote.isFormInline = this.isFormInline;
                this.setCheckoutSkipValidation();
            },
            validateShippingInformation: function () {
                var checkoutFields = window.customCheckoutFieldConfig.fields;
                if (!this.isFormInline && Object.keys(checkoutFields).length > 0 && checkoutFields['shipping-step'] !== undefined) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate', true);
                    if (this.source.get('params.invalid')) {
                        return !this.source.get('params.invalid'); 
                    }
                }
                return this._super();
            },
            setCheckoutSkipValidation: function () {
                skipState.setSkipValidation(!this.visible());
                this.visible.subscribe(function (flag) {
                    skipState.setSkipValidation(!flag);
                });
            }
        });
    };
});
