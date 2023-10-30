define([
    'jquery',
    'domReady!',
    'accordion',
    'mage/translate'
], function ($) {
    'use strict';

    init();

    function init() {
        $("body").on("click", ".accordion-step", function(){
            $(this).toggleClass("active");
            
            var stepElement = $(this).attr("data-step");

            $("#" + stepElement).fadeToggle("slow");
        });
        
        $('.minicart-items-wrapper').on('load', function(){
            console.log("seller-name: " + $('.items-in-cart .product-item .seller-name').length);
        });
        
        $('.minicart-items').on('load', function(){
            console.log("seller-name: " + $('.items-in-cart .seller').length);
        });

    }
});

