define([
    'jquery'
], function ($) {
    var cashbackLabel = $('.product-info-main').next('block-extendedrule');
    var productInfoPriceBlock = $('.product-info-price');
    if(cashbackLabel){
        productInfoPriceBlock.find('.price-box.price-final_price').addClass('cashback-active');
        alert($('.price-box.price-final_price.cashback-active').find('.old-price-label').length);
        if($('.price-box.price-final_price.cashback-active').find('.old-price-label').length === 0)
        {
            alert("no old price");
            productInfoPriceBlock.find('.price-box.price-final_price').addClass('pad-top');
        }
        
        productInfoPriceBlock.prepend($('.block-extendedrule'));
    }
});
