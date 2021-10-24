define([
    'jquery'
], function ($) {
    //clint changes cashback
    alert("repostion");
    if( $('#main-product-qantas').length )
    {
        //$('.qantas-new-container').append($('.qantas-pts-wrapper'));
        $('#main-product-qantas').insertAfter($('.product-options-wrapper'));
    }
    //$('.secure-pay-container').insertAfter($('.product-add-form'));
    //$('.widget-product').insertAfter($('.secure-pay-container'));
    $('.secure-pay-container').insertAfter($('.braintree-paypal-logo'));
    $('.widget-product').insertAfter($('.product-add-form'));

    $('.product-info-price .current-price-wrapper').insertBefore($('#main-product-qantas'));

});
