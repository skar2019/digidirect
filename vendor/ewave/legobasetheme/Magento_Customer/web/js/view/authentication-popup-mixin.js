define([
    'ko'
], function (ko) {
    'use strict';

    var mixin = {
        isShowNewCustomerBlock: ko.observable(true),
        isShowLoginBlock: ko.observable(true)
    };

    return function (target) {
        return target.extend(mixin);
    };
});
