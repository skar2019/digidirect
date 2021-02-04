define([
    'jquery',
    'uiComponent',
    'ko',
    'mage/storage',
    'Magento_Customer/js/customer-data',
    'accordion'
], function ($, Component, ko, storage, customerData) {

    'use strict';

    return Component.extend({
        defaults: {
            template: 'Digidirect_GiftCard/gift-cards',
            visible: true
        },

        initialize: function () {
            this._super();
            
        },

    });
});
