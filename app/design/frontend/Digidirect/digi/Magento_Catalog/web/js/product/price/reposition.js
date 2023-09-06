//redeploy
//clint
define([
    'jquery'
], function ($) {
    //clint changes cashback
    //Redeploy
    
    if($('.s19-component').length != 0) {
        var windowsize = $(window).width();
        if (windowsize > 768) {
            $('.studio19-wrapper').attr("style", "display:block !important;");
        } else {
            $('.studio19-wrapper').attr("style", "display:flex !important;");
        }
    }
    
    $('.secure-pay-container').ready(function() {
        $(".secure-pay-container").attr("style", "display: none");
    });

    $('.fotorama__wrap').ready(function() {
        console.log("Fotorma has been loaded!");
        setTimeout(function() {
        $('.custom-preloader').attr("style", "display:none !important;");
        $('.gallery-placeholder').attr("style", "visibility: visible !important;");
        $('.digiseconds-overlay').attr("style", "display:block;");
        }, 3000);
    });

    $(window).on('load', function(){
        if ($(window).width() <= 768) {
            console.log("Test OnLoad : Window width is " + $(window).width());
        }
        $('#product-addtocart-button').removeAttr("title");
    });

    $('#itoris-pm-link-custom').on('click', function(){
        $('#itoris-pm-link').trigger("click");
        console.log("Changed To Trigger Click!");
    });
    
    $('#zip-custom').on('click', function(){
        $('.zip-widget__wrapper').trigger("click");
        console.log("ZIP Custom Clicked!");
    });

});
