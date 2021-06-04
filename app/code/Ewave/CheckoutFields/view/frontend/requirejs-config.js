/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Ewave_CheckoutFields/js/view/shipping-extend': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Ewave_CheckoutFields/js/view/billing-address-extend': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'Ewave_CheckoutFields/js/form/element/abstract-extend': true
            },
            'Magento_Ui/js/form/element/single-checkbox': {
                'Ewave_CheckoutFields/js/form/element/single-checkbox-extend': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Ewave_CheckoutFields/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Ewave_CheckoutFields/js/model/set-payment-information-mixin': true
            }
        }
    }
};
