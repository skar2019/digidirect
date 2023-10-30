define([
    'uiComponent'
], function (Component) {
    'use strict';

    var cmsPayments = window.cmsPayments;

    return Component.extend({
        cmsData: cmsPayments ? cmsPayments.content : '',
        isVisible: function () {
            
            console.log("cms-payments");
            console.log("seller-name: " + $('.items-in-cart .product-item .seller-name').length);
            console.log("seller: " + $('.items-in-cart .seller').length);

            return cmsPayments;
        }
    });
});
