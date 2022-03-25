/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/sidebar'
], function ($, Component, quote, stepNavigator, sidebarModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information'
        },
        getShippingMethodTitle: function () {
            var shippingMethod = quote.shippingMethod(),
                shippingMethodTitle = '';

            if (!shippingMethod) {
                return '';
            }

            shippingMethodTitle = shippingMethod['carrier_title'];

            if (typeof shippingMethod['method_title'] !== 'undefined') {
                shippingMethodTitle += ' - ' + shippingMethod['method_title'];
            }

            return 'Shipping Method Renamed!';
        }
    });
});
