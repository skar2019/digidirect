define([
    'jquery'
], function ($) {
    //clint changes cashback
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
    var cashbackLabel = $('.product-info-main').next('block-extendedrule');
    var productInfoPriceBlock = $('.product-info-price');
    if(cashbackLabel){
        productInfoPriceBlock.find('.price-box.price-final_price').addClass('cashback-active');
        if($('.price-box.price-final_price.cashback-active').find('.old-price-label').length === 0)
        {
            productInfoPriceBlock.find('.price-box.price-final_price').addClass('pad-top');
        }

        if( $('.product-info-price .current-price-wrapper').length )         // use this if you are using class to check
        {
            $('.block-extendedrule').insertBefore( $('#main-product-qantas'));

        }
        else
        {
            $('.block-extendedrule').insertBefore( $('#main-product-qantas'));
        }


    }
});
