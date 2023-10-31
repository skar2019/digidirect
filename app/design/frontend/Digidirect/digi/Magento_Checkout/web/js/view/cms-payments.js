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
            
            $('.opc-sidebar').find('.seller-name').each(function(){
                var thisSellerPerItem = $(this);
                var sellerPerItem = $(this).html();
                console.log("sellerPerItem: " + sellerPerItem);
                $('.opc-sidebar').find('.seller').each(function(){
                    var thisSeller = $(this);
                    var seller = $(this).html();
                    console.log("seller: " + seller);
                    if (sellerPerItem === seller) {
                        console.log("sellerPerItem === seller");
                        $(thisSellerPerItem).parent().parent().insertAfter(thisSeller);
                    }
                });
            });

            return cmsPayments;
        }
    });
});
