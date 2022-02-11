//clint
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
    $('.braintree-paypal-logo').insertAfter($('.product-options-bottom'));
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

    if (isMobile) {
        $('.page-title-wrapper').insertBefore($('.media-area'));

    }

});
