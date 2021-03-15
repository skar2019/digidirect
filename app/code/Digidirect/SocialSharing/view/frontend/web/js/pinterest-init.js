define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('ewave.pinterestInit', {
        options: {
            source: '//assets.pinterest.com/js/pinit.js'
        },

        _create: function () {
            window.PinUtils ? this.reinintButton() : this.loadScript(); 
        },

        /**
         * Load script
         */
        loadScript: function () {
            require([this.options.source]);
        },

        /**
         * Reinit button
         */
        reinintButton: function () {
            window.PinUtils.build();
        }
    });

    return $.ewave.pinterestInit;
});
