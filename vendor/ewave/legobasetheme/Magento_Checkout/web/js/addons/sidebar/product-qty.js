(function () {
    'use strict';
    var modules = window.enabledModules;
    if (modules && modules.length && modules.indexOf('Ewave_ProductQty') != -1) {
        define(function (require) {
            require('sidebarProductQty');
        });
    }
}());
