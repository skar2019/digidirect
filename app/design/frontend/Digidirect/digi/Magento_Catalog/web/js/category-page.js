//rondel
define([
    'jquery'
], function ($) {
    window.onload = function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        $('.category-page-banner').attr("style", "display: block !important; margin-left: -55px; width: 1095px;");
        $('.custom-preloader-container').attr("style", "display: none !important;");
    };
    
    $(document).on("ready", ".sidebar-main", function(){
        console.log("Sidebar Ready Now!");   
        $(".custom-preloader-container").css("margin-left","-55px");   
    });
});
