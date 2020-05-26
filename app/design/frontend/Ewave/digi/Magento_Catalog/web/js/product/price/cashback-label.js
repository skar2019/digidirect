define([
    'jquery'
], function ($) {
    var cashbackLabel = $('.product-info-main').next('block-extendedrule');
    var productInfoPriceBlock = $('.product-info-price');
    if(cashbackLabel){
        productInfoPriceBlock.find('.price-box.price-final_price').addClass('cashback-active');
        productInfoPriceBlock.prepend($('.block-extendedrule'));
    }
});
