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
            $('#main-product-qantas').insertBefore($('.product-options-bottom'));
        }
    }

    //$('.secure-pay-container').insertAfter($('.product-add-form'));
    //$('.widget-product').insertAfter($('.secure-pay-container'));
    $('.secure-pay-container').insertAfter($('.braintree-paypal-logo'));
    $('.widget-product').insertAfter($('.product-add-form'));

    $('.product-info-price .current-price-wrapper').insertBefore($('#main-product-qantas'));

});
