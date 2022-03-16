
//clint
define([
    'jquery'
], function ($) {
    //clint changes cashback
    //Redeploy
    $('.secure-pay-container').ready(function() {
        $(".secure-pay-container").attr("style", "display: none");
        $('#product-options-wrapper').insertAfter($('.box-tocart'));
        $('.studio19-wrapper').insertAfter($('#product-options-wrapper'));
    });
    
    $('.fotorama-item').ready(function() {
        $('.custom-preloader').attr("style", "display:none !important;");
        $('.gallery-placeholder').attr("style", "visibility: visible !important;");
    });
    
    $(window).load(function(){
        if ($(window).width() <= 768) {
            $('.testfreaks-badge').insertAfter($('.page-title'));
            $('.product-info-price>.product.attribute.sku').insertAfter($('.testfreaks-badge'));
            console.log("Test OnLoad : Window width is " + $(window).width());
        } else {

        } 
    });
    
    if( $('#main-product-qantas').length )
    {
        //$('.qantas-new-container').append($('.qantas-pts-wrapper'));
        if($('.product-options-wrapper').length) {
            $('#main-product-qantas').insertAfter($('.product-options-wrapper'));
        }
        else {
            $('#main-product-qantas').insertBefore($('.box-tocart'));
        }
    }

    //$('.secure-pay-container').insertAfter($('.product-add-form'));
    //$('.widget-product').insertAfter($('.secure-pay-container'));
    $('.braintree-paypal-logo').insertAfter($('.box-tocart'));
    $('.secure-pay-container').insertAfter($('.braintree-paypal-logo'));
    $('.widget-product').insertAfter($('.product-add-form'));

    //$('.product-info-price .current-price-wrapper').insertBefore($('#main-product-qantas'));
    if($('#leftmenu').contents().length == 0) {

    }

    if ($('.studio19-wrapper').is(':empty')){
        //$('.studio19-wrapper').addClass('studio19-hide');
        $('.studio19-wrapper').attr("style", "display:none !important;");
    }

    //for mobile = 760
    //Changed to 1439 for tablet *Rondel
    let isMobile = window.matchMedia("only screen and (max-width: 1439px)").matches;

//    if (isMobile) {
//        $('.page-title-wrapper').insertBefore($('.product-basic'));
//    }else {
//        $('.page-title-wrapper').insertBefore($('.product-info-price'));
//    }
    
    $(window).on('resize', function(){
        var win = $(this); //this = window
        if (win.width() >= 1440) {
            $('.page-title-wrapper').insertBefore($('.product-info-price'));
        } else {
            $('.page-title-wrapper').insertBefore($('.product-basic'));
        } 
        
        if (win.width() <= 768) {
            //$('.testfreaks-badge').insertAfter($('.page-title'));
            //$('.product-info-price>.product.attribute.sku').insertBefore($('.testfreaks-badge'));
        } else {
            //$('.testfreaks-badge').insertAfter($('.page-title'));
            //$('.product-info-price>.product.attribute.sku').insertBefore($('.testfreaks-badge'));
        } 
    });
    
    $('#live-chat-additional-link-id').click(function(){
        $('[data-garden-id="buttons.icon_button"]').trigger("click");
        console.log("Changed To Trigger Click!");
    });

});
