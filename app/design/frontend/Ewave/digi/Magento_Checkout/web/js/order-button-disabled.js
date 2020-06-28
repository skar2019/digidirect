define([
    'jquery'
], function ($) {
    'use strict';

    $(window).click(function () {
        if ($('#payment-method-braintree-paypal').hasClass('_active')) {
            $('.single-actions').css('display', 'none');
        } else {
            $('.single-actions').css('display', 'block');
        }
    });

    $('.opc-progress-bar-item span').click(function () {
        $(".payment-method-braintree").removeClass('_active');
        if ($('#braintree_paypal').is(':checked')) {
            $('#payment-method-braintree-paypal').removeClass('_active');
        }
        $('.single-actions').css('display', 'block');
    })
});
