/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'addressVerification': 'Ewave_AddressVerification/js/autocomplete'
        }
    },
    config: {
        mixins: {
            'mage/menu': {
                'Ewave_AddressVerification/js/lib/mage/menu-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Ewave_AddressVerification/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ewave_AddressVerification/js/view/shipping-mixin': true
            }
        }
    },
    deps: [
        'babelpolyfill'
    ]
};
