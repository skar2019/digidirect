define([
    'uiComponent'
], function (Component) {
    'use strict';

    var cmsPayments = window.cmsPayments;

    return Component.extend({
        cmsData: cmsPayments ? cmsPayments.content : '',
        isVisible: function () {
            
            console.log("cms-payments");

            $('.minicart-items-wrapper').on('load', function(){
                console.log("seller-name: " + $('.items-in-cart .product-item .seller-name').length);
            });

            $('.minicart-items').on('load', function(){
                console.log("seller: " + $('.items-in-cart .seller').length);
            });

            return cmsPayments;
        }
    });
});
