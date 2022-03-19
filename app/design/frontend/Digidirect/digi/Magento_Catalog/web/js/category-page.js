
//rondel
define([
    'jquery'
], function ($) {
    
    $('#catalogSidebar').append('<div class="custom-preloader-container"><div class="custom-preloader"></div></div>');
    
    window.onload = function() {   
        
    };
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
        $('.custom-preloader-container').attr("style", "display: none !important;");
    });
    
});
