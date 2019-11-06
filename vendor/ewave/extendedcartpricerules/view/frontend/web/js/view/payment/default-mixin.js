define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/checkout-data'
], function ($, ko, quote, paymentService, checkoutData) {
    'use strict';

    var mixin = {
        initialize: function () {
            this._super();

            if (this.isRemoveItemRuleApplied()) {
                this.isPlaceOrderActionAllowed(false);
                this.isEnabledPaymentButton(false);
            }
            quote.totals.subscribe(function () {
                if (this.isRemoveItemRuleApplied()) {
                    this.resolvePaymentMethod(false);
                } else {
                    this.resolvePaymentMethod(quote.billingAddress() != null);
                }
            }, this);
            quote.billingAddress.subscribe(function (address) {
                if (this.isRemoveItemRuleApplied()) {
                    this.isPlaceOrderActionAllowed(false);
                    this.isEnabledPaymentButton(false);
                } else {
                    this.isPlaceOrderActionAllowed(address !== null);
                    this.isEnabledPaymentButton(address !== null);
                }
            }, this);

            return this;
        },
        placeOrder: function (data, event) {
            if (this.isRemoveItemRuleApplied()) {
                if (event) {
                    event.preventDefault();
                }
                this.isPlaceOrderActionAllowed(false);
                return false;
            } else {
                return this._super(data, event);
            }
        },
        selectPaymentMethod: function () {
            this._super();

            if (this.isRemoveItemRuleApplied()) {
                this.isPlaceOrderActionAllowed(false);
                this.isEnabledPaymentButton(false);
            } else {
                this.isPlaceOrderActionAllowed(quote.billingAddress() != null);
                this.isEnabledPaymentButton(quote.billingAddress() != null);
            }

            return true;
        },
        resolvePaymentMethod: function (isEnable) {
            var self = this,
                availablePaymentMethods = paymentService.getAvailablePaymentMethods(),
                selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();

            if (selectedPaymentMethod) {
                availablePaymentMethods.some(function (payment) {
                    if (payment.method == selectedPaymentMethod) {
                        self.isPlaceOrderActionAllowed(isEnable);
                        self.isEnabledPaymentButton(isEnable);
                    }
                });
            }
        },
        isRemoveItemRuleApplied: function () {
            var isRemoveItemRuleApplied = false;

            $.each(quote.getTotals()()['items'], function (key, value) {
                if (value.extension_attributes !== undefined && value.extension_attributes.is_remove_item_rule_applied) {
                    isRemoveItemRuleApplied = true;
                    return true;
                }
            });

            return isRemoveItemRuleApplied;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
