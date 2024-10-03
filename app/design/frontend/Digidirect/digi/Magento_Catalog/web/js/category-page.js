//rondel
//Redeploy
define([
    'jquery'
], function ($) {
    
    //$('.columns').append('<div class="custom-preloader-container""><div class="custom-preloader"></div></div>');
    //window.onload = function() {   
        
    //};
    
    $('.products-grid').ready(function() {
        $('.desktop-row').attr("style", "visibility: visible !important;");
        setTimeout(function() {
            $('.block-content.filter-content').attr("style", "display: block !important;").fadeIn();
        }, 3000);
    });

    // https://www.digidirect.com.au/lenses
    var str1 = $('.banner-content').html().replace('free Australian shipping on orders over $99', '');
    $('.banner-content').html(str1);

    // https://www.digidirect.com.au/cameras/film-cameras
    // https://www.digidirect.com.au/cameras/medium-format-cameras
    // https://www.digidirect.com.au/lenses/rangefinder-lenses
    // https://www.digidirect.com.au/lenses/special-effects-lenses
    // https://www.digidirect.com.au/photo-accessories/usb-cables-and-adapters
    // https://www.digidirect.com.au/audio-and-visual/headphones
    var str2 = $('.banner-content').html().replace('and free', '');
    $('.banner-content').html(str2);

    // https://www.digidirect.com.au/lenses/mirrorless-lenses
    var str3 = $('.banner-content').html().replace('For a limited time, get free shipping when you spend over $99. Otherwise,', '');
    $('.banner-content').html(str3);

    // https://www.digidirect.com.au/lenses/rangefinder-lenses
    // https://www.digidirect.com.au/photo-accessories/batteries-and-power-supplies
    var str4 = $('.banner-content').html().replace('free', '');
    $('.banner-content').html(str4);

    // https://www.digidirect.com.au/optics/binoculars-and-accessories
    var str5 = $('.banner-content').html().replace('Enjoy free shipping on orders over $99.', '');
    $('.banner-content').html(str5);

    // https://www.digidirect.com.au/optics/telescopes
    var str6 = $('.banner-content').html().replace('. digiDirect offers free shipping on all orders over $99', '');
    $('.banner-content').html(str6);

    // https://www.digidirect.com.au/audio-and-visual/audio-interfaces-and-mixers
    var str7 = $('.banner-content').html().replace('With free shipping on orders over $99 and Australia wide coverage, there’s nothing to lose!', '');
    $('.banner-content').html(str7);

    // https://www.digidirect.com.au/audio-and-visual/software
    var str8 = $('.banner-content').html().replace('We provide free shipping on orders over $99.', '');
    $('.banner-content').html(str8);

    // https://www.digidirect.com.au/audio-and-visual/drawing-tablets
    var str9 = $('.banner-content').html().replace(' and free shipping on orders over $99', '');
    $('.banner-content').html(str9);

    // https://www.digidirect.com.au/pro-video/white-balance-and-calibration
    var str10 = $('.banner-content').html().replace(' – you’ll enjoy free shipping on all orders over $99', '');
    $('.banner-content').html(str10);



});
