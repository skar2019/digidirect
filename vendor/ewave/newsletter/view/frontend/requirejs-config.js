/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Ewave_Newsletter/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Ewave_Newsletter/js/model/set-payment-information-mixin': true
            }
        }
    }
};
