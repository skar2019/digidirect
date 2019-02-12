define([
    'jquery',
    'jquery/ui',
    'priceBundle'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.productSummary', widget, {
            /**
             * Method attaches event observer to the product form
             * @private
             */
            _create: function () {
                var $mainContainer = this.element.closest(this.options.mainContainer);
                $mainContainer.on('updateProductSummary', $.proxy(this._renderSummaryBox, this));
                $mainContainer.priceBundle('updateProductSummary');
            }
        });

        return $.mage.productSummary;
    }
});
