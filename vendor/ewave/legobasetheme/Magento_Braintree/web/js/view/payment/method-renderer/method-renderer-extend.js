define(function () {
    'use strict';

    var mixin = {
        defaults: {
            isDefaultCheckbox: true,
            isVisisbleMessage: false
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});