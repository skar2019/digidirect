
//rondel
define([
    'jquery'
], function ($) {
    
    window.onload = function() {   
        
    };
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.custom-preloader-container').attr("style", "display: none !important;");
        $('.sidebar-main').attr("style", "display: block !important;").fadeIn();
    });
    
});
