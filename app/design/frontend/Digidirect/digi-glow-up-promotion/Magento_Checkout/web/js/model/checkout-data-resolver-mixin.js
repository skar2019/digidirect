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

            // Check if user has explicitly selected a method previously
            var hasExplicitSelection = selectedShippingRate || quote.shippingMethod();

            // Only auto-select for Click & Collect (single rate scenario)
            if (ratesData.length === 1) {
                var singleRate = ratesData[0];
                // Only auto-select if it's Click & Collect
                if (singleRate.carrier_code === 'collect' && singleRate.method_code === 'collect') {
                    selectShippingMethodAction(singleRate);
                    return;
                }
                // For other single rates, don't auto-select
                selectShippingMethodAction(null);
                return;
            }

            // If user previously selected a method, check if it's still available
            if (quote.shippingMethod()) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] == quote.shippingMethod()['carrier_code'] &&
                        rate['method_code'] == quote.shippingMethod()['method_code'];
                });
            }

            if (!availableRate && selectedShippingRate) {
                availableRate = _.find(ratesData, function (rate) {
                    return rate['carrier_code'] + '_' + rate['method_code'] === selectedShippingRate;
                });
            }

            // Only restore previously selected method if it's still available
            if (availableRate && hasExplicitSelection) {
                selectShippingMethodAction(availableRate);
            } else {
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
