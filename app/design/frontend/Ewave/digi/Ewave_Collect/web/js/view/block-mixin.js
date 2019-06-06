define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'jquery',
    'Magento_Checkout/js/model/cart/estimate-service',
    'Magento_Ui/js/lib/view/utils/async'
], function (ko, Component, quote, $, estimate, async) {
    'use strict';

    return function (target) {
        return target.extend({
            _isCheckoutPage: function() {
                return $('body').hasClass('checkout-index-index');
            },
            onSuccessApplyPlace: function (response) {
                this._super(response);
                var pageTypeIsCheckout = this._isCheckoutPage();
                if (pageTypeIsCheckout) {
                    window.selectStore(false);
                }
            },
            onSuccessDelivery: function () {
                this._super();
                this.toggleToDeliveryShippingMethod();
            },
            toggleToDeliveryShippingMethod: function () {
                var closestRadioButtons = $('.row.collect').siblings().find('input');

                if(!closestRadioButtons.filter(':checked').length) {
                    closestRadioButtons.first().trigger('click');
                }
            },
            setCollectAbstractEntityFields: function() {
                var self = this,
                    flag = true;
                this._super();
                if (this.isSingleCartCollectVariation() && this.collectPlaces() && !this.collectPlaces().length && this._isCheckoutPage()) {
                    async.async('.row .radio.-item-0', function (node) {
                        if (flag && $(node).is(':visible')) {
                            $(node).trigger('click');
                            flag = false;
                        }
                    });
                }
            },
            selectCollectShippingMethod: function () {
                var flag = true;

                async.async('div[data-collect-type="delivery"]', function (node) {
                    $(node).removeClass('-visible');
                });

                async.async('input[value="collect_collect"]', function (node) {
                    if (flag) {
                        $(node).trigger('click');
                        flag = false;
                    }
                });

                async.async('div[data-collect-type="collect"]', function (node) {
                    $(node).addClass('-visible');
                });
            }
        });
    }
});
