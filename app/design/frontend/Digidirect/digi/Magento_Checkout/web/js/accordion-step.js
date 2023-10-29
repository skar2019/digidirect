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
        
        $(window).on('load', function(){
            console.log("Jquery for seller!");
            $('.items-in-cart .product-item .seller-name').each(function(){
                console.log("Test each seller!");
            });
        });
 
    }
});

