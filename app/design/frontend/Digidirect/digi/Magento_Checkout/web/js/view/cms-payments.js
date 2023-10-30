define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    var cmsPayments = window.cmsPayments;

    return Component.extend({
        cmsData: cmsPayments ? cmsPayments.content : '',
        isVisible: function () {
            
            console.log("cms-payments");
            console.log("seller-name: " + $('.opc-sidebar .items-in-cart .product-item .seller-name').length);
            console.log("seller: " + $('.opc-sidebar .items-in-cart .seller').length);
            
            $('.opc-sidebar .items-in-cart .product-item .seller-name').each(function(){
                var sellerPerItem = $(this).html();
                console.log("sellerPerItem: " + sellerPerItem);
                $('.opc-sidebar .items-in-cart .seller').each(function(){
                    var seller = $(this).html();
                    console.log("seller: " + seller);
                    if (sellerPerItem == seller) {
                        $(sellerPerItem).closest('.product-item').insertAfter(seller);
                    }
                });
            });

            return cmsPayments;
        }
    });
});
