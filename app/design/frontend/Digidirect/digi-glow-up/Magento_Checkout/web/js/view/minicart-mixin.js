define([
    'Magento_Checkout/js/view/minicart'
], function (Component) {
    'use strict';

    return Component.extend({
        /**
         * Return product options for an item
         */
        getItemOptions: function (item) {
            if (item.options && item.options.length) {
                return item.options;
            }
            return [];
        }
    });
});
