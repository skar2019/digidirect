/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_SalesRule/js/view/summary/discount'
], function ($, _, Component, quote, discountView) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/summary/shipping'
        },
        quoteIsVirtual: quote.isVirtual(),
        totals: quote.getTotals(),
        
        getShippingMethodTitle: function () {
            return 'Shipping Method Renamed!';
        }
    });
});
