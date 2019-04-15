define(['mage/translate', 'Magento_Checkout/js/model/quote'], function ($t, quote) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                template: 'Ewave_SingleCheckoutButton/button',
                labelButtonShippingStep: $t('Continue'),
                labelButtonPaymentStep: $t('Place Your Order'),
                isOnlyPaymentStep: false,
                shippingFormSelector: '#co-shipping-method-form',
                paymentMethodContainer: '.payment-method',
                defaultButton: '.action.primary'
            }
        });
    };
});