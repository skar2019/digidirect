define(['mage/translate', 'Magento_Checkout/js/model/quote', 'jquery'], function ($t, quote, $) {
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
                defaultButton: '.action.primary',
                placeOrderButton: '#placeOrderBtn',
                brainTreeField: '.hosted-control',
                paymentMethod: '.payment-method-title > .radio'
            },

            paymentStepAction: function () {
                $(this.placeOrderButton).css('pointer-events', 'none');

                $(this.brainTreeField).on('mouseenter', function () {
                    $('#placeOrderBtn').css('pointer-events', 'auto');
                });

                $(this.brainTreeField).on('mouseleave', function () {
                    $('#placeOrderBtn').css('pointer-events', 'auto');
                });

                $(this.brainTreeField).on('touchstart', function () {
                    $('#placeOrderBtn').css('pointer-events', 'auto');
                });

                $(this.paymentMethod).on('click', function () {
                    $('#placeOrderBtn').css('pointer-events', 'auto');
                });

                this._super();
            }
        });
    };
});