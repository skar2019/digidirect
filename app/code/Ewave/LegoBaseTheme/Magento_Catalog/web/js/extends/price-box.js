define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.priceBox', widget, {
            _init: function initPriceBox () {
                if ($('body.catalog-product-view').length && this.element.find('.price-from').length) {
                    return false;
                } else {
                    this._super();
                }
            },
            _create: function createPriceBox () {
                if ($('body.catalog-product-view').length && this.element.find('.price-from').length) {
                    return false;
                } else {
                    this._super();
                }
            }
        });

        return $.mage.priceBox;
    };
});
