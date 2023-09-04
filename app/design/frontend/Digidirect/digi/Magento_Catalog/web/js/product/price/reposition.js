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

        if($('#product-options-wrapper').length) {
            //$('#product-options-wrapper').insertAfter($('.box-tocart'));
            //$('.studio19-wrapper').insertAfter($('#product-options-wrapper'));
        }else {
            //$('.studio19-wrapper').insertAfter($('.box-tocart'));
        }

        //$('#awaiting-product').insertAfter($('.box-tocart'));

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
            //$('.testfreaks-badge').insertAfter($('.page-title'));
            //$('.product-info-price>.product.attribute.sku').insertAfter($('.testfreaks-badge'));
            console.log("Test OnLoad : Window width is " + $(window).width());
        } else {

        }
        $('#product-addtocart-button').removeAttr("title");
        
        
        if( $('.itoris-pm-product-marker').length )
        {
            //$('.itoris-pm-product-marker').insertAfter($('.box-tocart'));
            //$(".itoris-pm-product-marker").attr("style", "display: block");
        }

    });

    if( $('#main-product-qantas').length )
    {
        //$('.qantas-new-container').append($('.qantas-pts-wrapper'));
        if($('.product-options-wrapper').length) {
            //$('#main-product-qantas').insertAfter($('.product-options-wrapper'));
        }
        else {
            //$('#main-product-qantas').insertBefore($('.box-tocart'));
        }
    }

    //$('.secure-pay-container').insertAfter($('.product-add-form'));
    //$('.widget-product').insertAfter($('.secure-pay-container'));
    //$('.braintree-paypal-logo').insertAfter($('.box-tocart'));
    //$('.secure-pay-container').insertAfter($('.braintree-paypal-logo'));
    //$('.widget-product').insertAfter($('.product-add-form'));

    //$('.product-info-price .current-price-wrapper').insertBefore($('#main-product-qantas'));
    if($('#leftmenu').contents().length == 0) {

    }
    
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

        //if (win.width() <= 768) {
        //$('.testfreaks-badge').insertAfter($('.page-title'));
        //$('.product-info-price>.product.attribute.sku').insertBefore($('.testfreaks-badge'));
        //} else {
        //$('.testfreaks-badge').insertAfter($('.page-title'));
        //$('.product-info-price>.product.attribute.sku').insertBefore($('.testfreaks-badge'));
        //}
    });
    
    if($('#product-options-wrapper .control').length) {
        $('#product-options-wrapper').attr("style", "display:block !important;");
    }

    $('#itoris-pm-link-custom').on('click', function(){
        // $('[data-garden-id="buttons.icon_button"]').trigger("click");
        //$('.itoris-pm-modal').addClass('_show');
        //$('.itoris-pm-modal').modal('toggle');
        $('#itoris-pm-link').trigger("click");
        console.log("Changed To Trigger Click!");
    });
    
    $('#zip-custom').on('click', function(){
        $('.zip-widget__wrapper').trigger("click");
        //$('.zip-container')[0].click();
        console.log("ZIP Custom Clicked!");
    });

});
