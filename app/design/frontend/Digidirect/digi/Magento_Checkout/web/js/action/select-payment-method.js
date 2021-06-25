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
            
            $(".checkout-qantas-wrapper .accordion-step").addClass("active");
            $(".checkout-qantas-wrapper .step-content").removeClass("hide");

            $(".comments-wrapper .accordion-step").addClass("active");
            $(".comments-wrapper .step-content").removeClass("hide");

            if ('method' in paymentMethod) {
                if(paymentMethod.method == "banktransfer" || paymentMethod.method == "zipmoneypayment" || paymentMethod.method == "klarna_pay_later"){
                    $(".checkout-payment-method .accordion-step").removeClass("active");
                    $(".checkout-payment-method .step-content").addClass("hide");
                }
            }
        }
        
        quote.paymentMethod(paymentMethod);
    };
});
