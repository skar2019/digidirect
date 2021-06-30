/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'Magento_Checkout/js/model/quote'
], function ($, quote) {
    'use strict';

    return function (paymentMethod) {
        if (paymentMethod) {
            paymentMethod.__disableTmpl = {
                title: true
            };

            if ('method' in paymentMethod) {
                if(paymentMethod.method == "banktransfer" || paymentMethod.method == "zipmoneypayment" || paymentMethod.method == "klarna_pay_later"){
                    $(".checkout-payment-method .accordion-step").removeClass("active");
                    $(".checkout-payment-method .step-content").fadeOut("slow", "swing");
                }
            }
        }
        
        quote.paymentMethod(paymentMethod);
    };
});
