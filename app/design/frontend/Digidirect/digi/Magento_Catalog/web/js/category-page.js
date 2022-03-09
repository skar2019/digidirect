//rondel
define([
    'jquery'
], function ($) {
    window.onload = function() {
        
    };
    
//    $("#catalogSidebar").ready(function(){
//        console.log("Sidebar Ready!");   
//        //$(".custom-preloader-container").css("margin-left","-55px");
//        $('.desktop-row').attr("style", "visibility: visible !important;");
//        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
//        $('.custom-preloader-container').attr("style", "display: none !important;");
//    });
//    
    if( $("#catalogSidebar").length ){
        console.log("Sidebar Exist!");   
        //$(".custom-preloader-container").css("margin-left","-55px");
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
        $('.custom-preloader-container').attr("style", "display: none !important;");
    }
    
});
