//rondel
define([
    'jquery'
], function ($) {
    
    window.onload = function() {   
        
    };
    
    $('.product-category-listing').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.category-page-banner').attr("style", "display: block; margin-left: -55px; width: 1095px;").fadeIn();
        $('.custom-preloader-container').attr("style", "display: none !important;");
        $('.sidebar-main').attr("style", "display: block !important;").fadeIn();
    });
    
});
