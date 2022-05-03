//rondel
//Redeploy
define([
    'jquery'
], function ($) {
    
    //$('.columns').append('<div class="custom-preloader-container""><div class="custom-preloader"></div></div>');
    window.onload = function() {   
        
    };
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        setTimeout(function() {
            $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
            console.log('Timeout Executed!');
        }, 3000);
        //$('.custom-preloader-container').attr("style", "display: none !important;");
    });
    
    $(".product-category-listing .product-items").bind("DOMSubtreeModified", function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        setTimeout(function() {
            $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
            console.log('Timeout Executed!');
        }, 3000);
    });
    
});
