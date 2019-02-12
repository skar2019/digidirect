define([
    'Magento_Checkout/js/model/quote'
], function (
    quote
) {
    'use strict';
    return function (target) {
        return target.extend({
            initialize: function () {
                this._super();
                quote.isFormInline = this.isFormInline;
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
            }
        });
    };
});
