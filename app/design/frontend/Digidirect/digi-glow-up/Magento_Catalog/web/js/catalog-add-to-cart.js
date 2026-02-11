define([
    'jquery',
    'mage/mage',
    'Magento_Catalog/js/catalog-add-to-cart'
], function($) {
    'use strict';

    return function(widget) {
        $.widget('mage.catalogAddToCart', widget, {
            // Override the submit handler
            submitForm: function(form) {
                var self = this;
                
                // Disable button IMMEDIATELY before AJAX
                self.disableAddToCartButton(form);
                
                // Call parent method
                this._super(form);
                console.log("submitForm");
            },

            // Override to disable immediately
            ajaxSubmit: function(form) {
                var self = this;
                
                // Disable button IMMEDIATELY
                self.disableAddToCartButton(form);
                
                // Call parent AJAX method
                this._super(form);
                console.log("ajaxSubmit");
            }
        });

        return $.mage.catalogAddToCart;
    };
});