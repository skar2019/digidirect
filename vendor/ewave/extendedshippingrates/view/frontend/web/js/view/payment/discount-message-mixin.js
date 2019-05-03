define([
    'underscore',
    'mage/translate',
    'Magento_SalesRule/js/view/payment/discount',
    'Ewave_ExtendedShippingRates/js/action/shipping-rates-updater'
], function (_, $t, discount, ratesUpdater) {
    'use strict';

    var mixin = {
        defaults: {
            appliedMessage: $t('Your coupon was successfully applied.'),
            canceledMessage: $t('Your coupon was successfully canceled.'),
            appliedCouponMessage: 'SETCOUPONECODE',
            canceledCouponMessage: 'REMOVECOUPONECODE'
        },

        onHiddenChange: function (isHidden) {
            this.checkErrorMessages();
            this._super(isHidden);
        },

        checkErrorMessages: function () {
            var newMessage = null,
                isApplied,
                errorMessages = this.messageContainer.errorMessages();

            if (_.contains(errorMessages, this.appliedCouponMessage)) {
                newMessage = this.appliedMessage;
                isApplied = true;
            }

            if (_.contains(errorMessages, this.canceledCouponMessage)) {
                newMessage = this.canceledMessage;
                isApplied = false;
            }

            if (newMessage) {
                this.replaceMessage(newMessage);
                this.setDiscountState(isApplied);
            }
        },

        replaceMessage: function (newMessage) {
            this.removeAll();
            this.messageContainer.successMessages.push(newMessage);
        },

        setDiscountState: function (isApplied) {
            discount().isApplied(isApplied);
            ratesUpdater.isNeedUpdate(true);
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
