//redeploy
//clint
define([
    'jquery'
], function ($) {
    //clint changes cashback
    //Redeploy
    
    if($('.s19-component').length != 0) {
        $('.studio19-wrapper').attr("style", "display:block !important;");
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
    
    $('.s19-price-per-period').insertBefore($('.s19-component'));
    $('.s19-apply-btn').insertBefore($('.s19-learn-btn'));
    $('.s19-actions').insertAfter($('.s19-min-period'));
    
    var win = $(this); //this = window
    if (win.width() >= 768) {
        $('.page-title-wrapper').insertBefore($('.product-info-price'));
    } else {
        $('.page-title-wrapper').insertBefore($('.media-area'));
    }

    //for mobile = 760
    //Changed to 1439 for tablet *Rondel
    let isMobile = window.matchMedia("only screen and (max-width: 1439px)").matches;

    $(window).on('resize', function(){
        if (win.width() >= 768) {
            $('.page-title-wrapper').insertBefore($('.product-info-price'));
        } else {
            $('.page-title-wrapper').insertBefore($('.media-area'));
        }
    });
    
    if($('#product-options-wrapper .control').length) {
        $('#product-options-wrapper').attr("style", "display:block !important;");
    }

    $('#itoris-pm-link-custom').on('click', function(){
        $('#itoris-pm-link').trigger("click");
        console.log("Changed To Trigger Click!");
    });
    
    $('#zip-custom').on('click', function(){
        $('.zip-widget__wrapper').trigger("click");
        console.log("ZIP Custom Clicked!");
    });

});
