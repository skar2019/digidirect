define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    'Digidirect_Collect/js/model/shipping-rates-validator/collect',
    'Digidirect_Collect/js/model/shipping-rates-validation-rules/collect'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    collectShippingRatesValidator,
    collectShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('collect', collectShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('collect', collectShippingRatesValidationRules);
    
    alert('Unavailable : ' + $('#pickup-unavailable .collectlocator-wrapper .locator-items .locator-item').length);

    return Component;
});
