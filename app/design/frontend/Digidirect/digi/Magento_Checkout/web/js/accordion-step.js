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
        
        console.log('seller-name count: ' + $('.items-in-cart .product-item .seller-name').length);
        console.log('seller count: ' + $('.items-in-cart .seller').length);
        
        $(window).on('load', function(){
            
            /*$('.items-in-cart .product-item .seller-name').each(function(){
                console.log("Test each seller!");
            });*/
            
            /*console.log("JS for seller!");
            var elements = document.getElementsByClassName("seller-name");
            var elementsArr = Array.from(elements);
            console.log("elementsArr: " + JSON.stringify(elementsArr));
            elementsArr.forEach(myFunction);

            function myFunction(currentValue, index) {
                console.log(currentValue.innerHTML);
            }*/
            
        });
 
    }
});

