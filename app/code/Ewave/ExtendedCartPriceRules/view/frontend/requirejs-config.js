/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'orderReview': 'Ewave_ExtendedCartPriceRules/js/extends/order-review'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/form/element/email': {
                'Ewave_ExtendedCartPriceRules/js/view/form/element/email': true
            },
            'Magento_Checkout/js/view/cart/shipping-estimation': {
                'Ewave_ExtendedCartPriceRules/js/view/cart/shipping-estimation-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ewave_ExtendedCartPriceRules/js/view/shipping-mixin': true
            },
            'Magento_Braintree/js/paypal/button': {
                'Ewave_ExtendedCartPriceRules/js/paypal/button-mixin': true
            },
            'Magento_Paypal/js/in-context/button': {
                'Ewave_ExtendedCartPriceRules/js/in-context/button-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'Ewave_ExtendedCartPriceRules/js/view/payment/default-mixin': true
            },
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Ewave_ExtendedCartPriceRules/js/view/payment/method-renderer/paypal-mixin': true
            }
        }
    }
};
