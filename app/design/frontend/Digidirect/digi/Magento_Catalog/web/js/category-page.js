
//rondel
define([
    'jquery'
], function ($) {
    
    $(document).ready(function() {
        $('.columns').append('<div class="custom-preloader-container""><div class="custom-preloader"></div></div>');
        console.log( "Document Loaded!" );
    });
    
    
    window.onload = function() {   
        
    };
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
        $('.custom-preloader-container').attr("style", "display: none !important;");
    });
    
});
