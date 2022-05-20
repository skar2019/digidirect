define(function () {
    'use strict';

    var mixin = {
        isActive: function () {
            var active = this.getCode() === this.isChecked();
            this.active(active);
            return active;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
