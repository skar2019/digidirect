//rondel
define([
    'jquery'
], function ($) {
    window.onload = function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
        $('.custom-preloader-container').attr("style", "display: none !important;");
    };
    
    $(".sidebar-main").ready(function(){
        console.log("Sidebar Ready!");   
        $(".custom-preloader-container").css("margin-left","-55px");
    });
});
