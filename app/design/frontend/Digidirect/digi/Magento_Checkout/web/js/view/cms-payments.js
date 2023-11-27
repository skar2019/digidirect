define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    var cmsPayments = window.cmsPayments;

    return Component.extend({
        cmsData: cmsPayments ? cmsPayments.content : '',
        isVisible: function () {
            
            //console.log("cms-payments");
            //console.log("seller-name: " + $('.opc-sidebar .items-in-cart .product-item .seller-name').length);
            //console.log("seller: " + $('.opc-sidebar .items-in-cart .seller').length);
            
            var group = 0;
            $('.items-in-cart').each(function(){
                group += 1;
                //console.log('group: ' + group);
                $(this).addClass('group-' + group);
            });
            
            $('.items-in-cart.active.group-1 .seller').each(function(){
                var thisSeller = $(this);
                var seller = $(this).html();
                //console.log("seller: " + seller);

                $('.items-in-cart.active.group-1 .seller-name').each(function(){
                    var thisSellerPerItem = $(this);
                    var sellerPerItem = $(this).html();
                    //console.log("sellerPerItem: " + sellerPerItem);

                    var subStrSpi = sellerPerItem.substring(
                        sellerPerItem.indexOf("-->") + 3, 
                        sellerPerItem.lastIndexOf("<!--")
                    );
                    //console.log("subStrSpi: " + subStrSpi);
                    
                    if (!thisSellerPerItem.hasClass("grouped")) {
                        if (subStrSpi === seller) {
                            thisSellerPerItem.parent().insertAfter(thisSeller.parent());
                            thisSellerPerItem.addClass('grouped');
                        }
                    }
                });
            });
            
            $('.items-in-cart.active.group-2 .seller').each(function(){
                var thisSeller = $(this);
                var seller = $(this).html();
                //console.log("seller: " + seller);

                $('.items-in-cart.active.group-2 .seller-name').each(function(){
                    var thisSellerPerItem = $(this);
                    var sellerPerItem = $(this).html();
                    //console.log("sellerPerItem: " + sellerPerItem);

                    var subStrSpi = sellerPerItem.substring(
                        sellerPerItem.indexOf("-->") + 3, 
                        sellerPerItem.lastIndexOf("<!--")
                    );
                    //console.log("subStrSpi: " + subStrSpi);

                    if (!thisSellerPerItem.hasClass("grouped")) {
                        if (subStrSpi === seller) {
                            thisSellerPerItem.parent().insertAfter(thisSeller.parent());
                            thisSellerPerItem.addClass('grouped');
                        }
                    }
                });
            });

            return cmsPayments;
        }
    });
});
