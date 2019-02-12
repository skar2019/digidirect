define(function () {
    'use strict';
    return function (target) {
        return target.extend({
            defaults: {
                creditCardHolder: ''
            },
            isShowCardHolder: function () {
                return false;
            }
        });
    };
});
