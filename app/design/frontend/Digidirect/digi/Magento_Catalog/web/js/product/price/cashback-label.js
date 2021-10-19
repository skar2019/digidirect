define([
    'jquery'
], function ($) {
    alert("cashback?");
    $('.qantas-pts-wrapper').insertAfter($('.product-options-wrapper'));

    $('.secure-pay-container').insertAfter($('.product-add-form'));

    $('.current-price-wrapper').insertBefore($('.qantas-pts-wrapper'));

    var cashbackLabel = $('.product-info-main').next('block-extendedrule');
    var productInfoPriceBlock = $('.product-info-price');
    if(cashbackLabel){
        productInfoPriceBlock.find('.price-box.price-final_price').addClass('cashback-active');
        if($('.price-box.price-final_price.cashback-active').find('.old-price-label').length === 0)
        {
            productInfoPriceBlock.find('.price-box.price-final_price').addClass('pad-top');
        }

        productInfoPriceBlock.append($('.block-extendedrule'));
    }
});
