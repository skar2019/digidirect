define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function (ko, Component, quote) {
    'use strict';

    return function (target) {
        return target.extend({
            onSuccessApplyPlace: function (response) {
                this._super(response);
                window.selectStore(false);
            }
        });
    }
});
