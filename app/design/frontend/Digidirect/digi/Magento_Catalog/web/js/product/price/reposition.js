//clint
define([
    'jquery'
], function ($) {
    //clint changes cashback

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
    $('.secure-pay-container').insertAfter($('.braintree-paypal-logo'));
    $('.widget-product').insertAfter($('.product-add-form'));

    $('.product-info-price .current-price-wrapper').insertBefore($('#main-product-qantas'));
    if($('#leftmenu').contents().length == 0) {
        $('.studio19-wrapper').hide();
    }

    //for mobile
    let isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;

    if (isMobile) {
        $('.page-title-wrapper').insertBefore($('.media-area'));

    }

});
