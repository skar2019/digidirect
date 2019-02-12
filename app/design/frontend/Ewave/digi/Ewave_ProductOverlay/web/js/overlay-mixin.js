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
            setOverlayStyle: function () {}
        });
        return $.ewave.productOverlay;
    };
});
