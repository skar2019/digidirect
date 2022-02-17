//clint
//Redeploy
define([
    'jquery'
], function ($) {
    //clint changes cashback
    
    window.onload = function() {
        $(".secure-pay-container").attr("style", "display: inline-block");
    };
      

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
        $('.studio19-wrapper').addClass('studio19-hide');
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
            $('.product-reviews-summary').insertAfter($('.page-title-wrapper'));
            $('.product-info-price>.product.attribute.sku').insertBefore($('.product-basic'));
        } else {
            //$('.product-info-main .product-reviews-summary').insertBefore($('.product-info-main .price-final_price'));
            //$('.product-info-main .product.attribute.sku').insertBefore($('.product-info-main .product-reviews-summary'));
        } 
    });

});
