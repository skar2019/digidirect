define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('ewave.productOverlay', widget, {
            /**
             * Disable inline styles calculations
             */
            setOverlayStyle: function () {
                // hide overlay if it does not have use for parent flag
                if (this.options.hideForConfigurable) {
                    this.element.addClass('-hide');
                }
            }
        });
        return $.ewave.productOverlay;
    };
});