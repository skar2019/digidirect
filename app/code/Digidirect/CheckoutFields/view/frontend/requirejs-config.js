/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Digidirect_CheckoutFields/js/view/shipping-extend': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Digidirect_CheckoutFields/js/view/billing-address-extend': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'Digidirect_CheckoutFields/js/form/element/abstract-extend': true
            },
            'Magento_Ui/js/form/element/single-checkbox': {
                'Digidirect_CheckoutFields/js/form/element/single-checkbox-extend': true
            }
        }
    }
};
