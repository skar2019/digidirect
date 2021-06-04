define([
    'mage/translate'
],
function ($t) {
    'use strict';
    var mixin = {
        itemHasPrice: function (item) {
            return item.extension_attributes && item.extension_attributes.ewave_is_free_gift_item ? !item.extension_attributes.ewave_is_free_gift_item : true;
        },

        getDefaultText: function (item) {
            return $t(item.extension_attributes.ewave_free_gift_price);
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
