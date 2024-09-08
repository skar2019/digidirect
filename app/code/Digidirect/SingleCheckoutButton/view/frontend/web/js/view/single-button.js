define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Digidirect_SingleCheckoutButton/js/view/error-methods',
    'uiRegistry',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-payment-method'
], function ($, ko, _, Component, stepNavigator, quote, $t, errorMethods, uiRegistry, checkoutData, selectPaymentMethodAction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Digidirect_SingleCheckoutButton/button',
            labelButtonShippingStep: $t('Next'),
            labelButtonPaymentStep: $t('Place order'),
            isOnlyPaymentStep: false,
            shippingFormSelector: '#co-shipping-method-form',
            paymentMethodContainer: '.payment-method',
            defaultButton: '.action.primary',
            isBraintreePaypalOneStepEnabled: true,
            braintreePaypalComponent: 'checkout.steps.billing-step.payment.payments-list.braintree_paypal',
            braintreePaypalButtonContinue: '#braintree_paypal_continue_to',
            braintreePaypalButtonPlaceOrder: '#braintree_paypal_place_order'
        },
        activeStep: ko.observable(null),
        label: ko.observable(null),
        isVisible: ko.observable(false),
        isDisabled: ko.observable(false),
        isSubscribed: false,

        initialize: function () {
            this._super();
            this.bind();

            return this;
        },

        /**
         * Bind
         */
        bind: function () {
            this.subscribeStepNavigator();
            this.subscribeActiveStep();
            this.subscribeBillingAddress();
        },

        subscribeStepNavigator: function () {
            stepNavigator.steps.subscribe(function (data) {
                this.checkActiveStep(data);
            }, this);
        },

        subscribeActiveStep: function () {
            this.activeStep.subscribe(function (step) {
                this.setButtonData(step);
            }, this);
        },

        subscribeBillingAddress: function () {
            quote.billingAddress.subscribe(function (newAddress) {
                this.isDisabled(!newAddress);
            }, this);
        },

        /**
         * Check active checkout step
         * @param {array} data
         */
        checkActiveStep: function (data) {
            var step = _.find(data, function (item) {
                    return item.isVisible();
                }),
                hash;

            if (!this.activeStep() && step || step && this.activeStep !== step.code) {
                hash = window.location.hash;
                if (hash && hash.replace('#', '') !== step.code) {
                    this.activeStep(hash.replace('#', ''));
                } else {
                    this.activeStep(step.code);
                }
            } else if (typeof step === 'undefined' && data.length) {
                this.activeStep(data[data.length - 1].code);
            }
        },

        /**
         * Set button data
         * @param {string} step
         */
        setButtonData: function (step) {
            var labelButton = step === 'shipping' ? this.labelButtonShippingStep : this.labelButtonPaymentStep,
                visible = this.isOnlyPaymentStep ? step === 'payment' : true;
            this.label(labelButton);
            this.isVisible(visible);
            this.isDisabled(this.activeStep() === 'payment' && !quote.billingAddress());
        },

        /**
         * Button action
         */
        singleButtonAction: function () {
            this.activeStep() === 'shipping' ? this.shippingStepAction() : this.paymentStepAction();
        },
        
        checkoutTrigger: function () {
            console.log("Checkout Trigger!");
                
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            var customerId = getCookie('PAC');
            var sessionId = getCookie('pa_session_id');
            var currentUrl = window.location.href;

            var configCustomerId;
            var configSessionId;

            configCustomerId = customerId;
            configSessionId = sessionId;

            var date = new Date();
            var now_utc = Date.UTC(date.getUTCFullYear(), date.getUTCMonth(),
                    date.getUTCDate(), date.getUTCHours(),
                    date.getUTCMinutes(), date.getUTCSeconds());
            var products = [];

            /*$("#mini-cart .product-item").each(function() {

                var productItem;

                var refId = $(this).find("[pa-option-label='refId']").attr("pa-option-value"); 
                var quantity = $(this).find(".cart-item-qty").attr("data-item-qty"); 
                var routeId = $(this).find("[pa-option-label='routeId']").attr("pa-option-value"); 
                var widgetId = $(this).find("[pa-option-label='widgetId']").attr("pa-option-value"); 

                productItem = {"refId": refId, "quantity": quantity, "routeId": routeId, "widgetId": widgetId};
                products.push(productItem);

            });

            var total = $("#minicartSidebar .subtotal .price").text();
            var finalTotal = total.substr(1);

            const checkoutData = {
                customerId: configCustomerId,
                sessionId: configSessionId,
                events: [
                    {
                        currentUrl: currentUrl,
                        eventTime: date.toISOString(),
                        products: products,
                        subTotal: finalTotal,
                        totalPrice: finalTotal,
                        currencyCode: "AUD"
                    }
                ]
            };

            if (products) {
                console.log("checkoutData", JSON.stringify(checkoutData));
            }*/
        },

        /**
         * Shipping step action
         */
        shippingStepAction: function () {
            selectPaymentMethodAction(null);
            checkoutData.setSelectedPaymentMethod(null);
            this.isSubscribed = false;
            $(this.shippingFormSelector).trigger('submit');
        },

        /**
         * Payment step action
         */
        paymentStepAction: function () {            
            if (this.checkPaymentMethod() && quote.paymentMethod().method === 'braintree_paypal') {
                $(this.braintreePaypalButtonContinue).trigger('click');
                if (!this.isSubscribed) {
                    this.subscribePaypalBraintreeResponse();
                }
            } else {
                this.checkPaymentMethod() ? $('#' + quote.paymentMethod().method).closest(this.paymentMethodContainer).find(this.defaultButton).trigger('click') : errorMethods().isErrorPaymentMethod(true);
            }
        },

        /**
         * Check payment method is chosen
         * @return {boolean}
         */
        checkPaymentMethod: function () {
            return !!quote.paymentMethod();
        },

        /**
         * Subscribe to Braintree PayPal response
         */
        subscribePaypalBraintreeResponse: function () {
            var component = uiRegistry.get(this.braintreePaypalComponent);

            component.isReviewRequired.subscribe(function (flag) {
                flag && component.paymentMethodNonce ? this.onSuccessGetNonce() : this.onErrorGetNonce();
            }, this);

            this.isSubscribed = true;
        },

        /**
         * Success callback
         */
        onSuccessGetNonce: function () {
            this.isBraintreePaypalOneStepEnabled ? $(this.braintreePaypalButtonPlaceOrder).trigger('click') : this.proceedSecondStep();
        },

        /**
         * Error callback
         */
        onErrorGetNonce: function () {},

        /**
         * Callback for second step PayPal Braintree
         */
        proceedSecondStep: function () {}
    });
});
