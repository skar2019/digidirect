
//rondel
define([
    'jquery'
], function ($) {
    
    $('#catalogSidebar').append('<div class="custom-preloader-container" style="margin-top: 70px;margin-left: 25px;">\n\
    <div class="custom-preloader" style="display: block;margin: auto;vertical-align: middle;border: 7px solid #f5f5f5;border-radius: 50%;border-top: 7px solid #000;width: 50px;height: 50px;-webkit-animation: spin 2s linear infinite; /* Safari */animation: spin 2s linear infinite;"></div></div>');
    
    window.onload = function() {   
        
    };
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
        //$('.custom-preloader-container').attr("style", "display: none !important;");
    });
    
});
