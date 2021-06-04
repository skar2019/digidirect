define([
    'underscore'
], function (_) {
    'use strict';

    var mixin = {
        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            if (updatedCart.items && !_.isEmpty(updatedCart.items)) {
                _.each(updatedCart.items, function (item) {
                    if (item.product_total_price) {
                        item.product_price = item.product_total_price;
                    }
                })
            }
            this._super(updatedCart);
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});