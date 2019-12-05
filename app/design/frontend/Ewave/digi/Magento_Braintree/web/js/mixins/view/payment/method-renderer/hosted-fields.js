define(['jquery'], function ($) {
    'use strict';

    var mixin = {
        placeOrderClick: function () {
            this._super();
            if (this.validateCardType()) {
                $(this.getSelector('submit')).prop('disabled', true);
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
