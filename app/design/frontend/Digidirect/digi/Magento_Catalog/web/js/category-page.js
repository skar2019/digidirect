//rondel
define([
    'jquery'
], function ($) {
    
    window.onload = function() {
        console.log("On Load!");   
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
        $('.custom-preloader-container').attr("style", "display: none !important;");
        $('.sidebar-main').attr("style", "display: block !important;");
    };
    
//    $("#catalogSidebar").ready(function(){
//        console.log("Sidebar Ready!");   
//        $('.desktop-row').attr("style", "visibility: visible !important;");
//        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
//        $('.custom-preloader-container').attr("style", "display: none !important;");
//    });
    
    
});
