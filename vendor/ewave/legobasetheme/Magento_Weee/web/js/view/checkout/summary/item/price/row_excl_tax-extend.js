define(function () {
    'use strict';
    var mixin = {
        itemHasPrice: function (item) {
            return true;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
})