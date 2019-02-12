/* eslint no-unused-vars: [1] */

var config = {
    map: {
        '*': {
            'orderReviewCollect': 'Ewave_Collect/js/order-review-collect',
            'collectBlock': 'Ewave_Collect/js/collect-block',
            'collectCart': 'Ewave_Collect/js/collect-cart',
            'collectModal': 'Ewave_Collect/js/collect-modal'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/quote': {
                'Ewave_Collect/js/model/quote-mixin': true
            },
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Ewave_Collect/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor/default': {
                'Ewave_Collect/js/model/shipping-save-processor/default-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ewave_Collect/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'Ewave_Collect/js/view/shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Ewave_Collect/js/view/billing-address-mixin': true
            }
        }
    }
};
