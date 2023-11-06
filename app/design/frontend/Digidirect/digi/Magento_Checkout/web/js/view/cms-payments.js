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
            
            $('.opc-sidebar .seller-name').each(function(){
                var thisSellerPerItem = $('.opc-sidebar .seller-name');
                var sellerPerItem = $('.opc-sidebar .seller-name').html();
                console.log("sellerPerItem: " + sellerPerItem);
                
                var subStrSpi = sellerPerItem.substring(
                    sellerPerItem.indexOf("-->") + 3, 
                    sellerPerItem.lastIndexOf("<!--")
                );
        
                console.log("subStrSpi: " + subStrSpi);
                
                $('.opc-sidebar .seller').each(function(){
                    var thisSeller = $('.opc-sidebar .seller');
                    var seller = $('.opc-sidebar .seller').html();
                    console.log("seller: " + seller);
                    if (subStrSpi === seller) {
                        $(thisSellerPerItem).parent().insertAfter(thisSeller.parent());
                    }
                });
            });

            return cmsPayments;
        }
    });
});
