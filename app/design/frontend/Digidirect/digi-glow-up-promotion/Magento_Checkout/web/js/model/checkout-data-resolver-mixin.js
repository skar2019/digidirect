define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Customer/js/customer-data'
], function (
    $,
    quote,
    checkoutData,
    selectShippingMethodAction,
    customerData
) {
    'use strict';
    return function (target) {
        target.resolveShippingRates = function (ratesData) {
            var selectedShippingRate = checkoutData.getSelectedShippingRate(),
                availableRate = false;

            // console.log('=== resolveShippingRates called ===');
            // console.log('Rates data length:', ratesData.length);
            // console.log('Current quote.shippingMethod():', quote.shippingMethod());
            // console.log('Saved selectedShippingRate:', selectedShippingRate);

            // CRITICAL: Only has explicit selection if BOTH conditions are true:
            // 1. There's a saved rate in localStorage
            // 2. quote.shippingMethod() is currently set (not null)
            var hasExplicitSelection = selectedShippingRate && quote.shippingMethod();

            // Only auto-select for Click & Collect (single rate scenario)
            if (ratesData.length === 1) {
                var singleRate = ratesData[0];
                // Only auto-select if it's Click & Collect
                if (singleRate.carrier_code === 'collect' && singleRate.method_code === 'collect') {
                    //console.log('Auto-selecting Click & Collect (only option)');
                    selectShippingMethodAction(singleRate);
                    return;
                }
                // For other single rates, don't auto-select
                //console.log('Single rate but not C&C, not auto-selecting');
                selectShippingMethodAction(null);
                return;
            }

            // If user previously selected a method AND it's currently set, check if it's still available
            if (quote.shippingMethod()) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] == quote.shippingMethod()['carrier_code'] &&
                        rate['method_code'] == quote.shippingMethod()['method_code'];
                });
            }

            if (!availableRate && selectedShippingRate && quote.shippingMethod()) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] + '_' + rate['method_code'] === selectedShippingRate;
                });
            }

            // Only restore previously selected method if it's still available AND currently set
            if (availableRate && hasExplicitSelection) {
                //console.log('Restoring previously selected method:', availableRate);
                selectShippingMethodAction(availableRate);
            } else {
                //console.log('No auto-selection - user must choose');
                // No auto-selection - user must choose
                selectShippingMethodAction(null);
            }

            function checkoutMobileCart () {
                var checkoutOffCanvasBar = document.getElementById('checkoutOffCanvasBarContent'),
                    checkoutOffCanvasSummary = document.getElementById('mobileCheckout'),
                    checkoutOffCanvasItems = $('#mobileCheckout .items-in-cart'),
                    checkoutOffCanvasButton = $('#mobileCheckout .single-actions .button');

                // Move items copy to Right Bar on Checkout page
                $(checkoutOffCanvasItems).appendTo(checkoutOffCanvasBar);

                // Move summary copy to Right Bar on Checkout page
                $(checkoutOffCanvasSummary).appendTo(checkoutOffCanvasBar);

                var btn = $('#checkoutOffCanvasBarContent .button.action');
                btn.on('click',function () {
                    var checkoutCartContentBar = document.getElementById('checkoutOffCanvasBar');
                    var actionLeftBlock = document.getElementById('leftBlock');

                    checkoutCartContentBar.classList.remove('-bar-open');
                    actionLeftBlock.classList.remove('-bar-open');
                })
            }

            checkoutMobileCart()
        };

        return target;
    };
});
