define([
    'jquery',
    'Magento_Theme/js/messages'  // extend core
], function ($, originalMessages) {
    'use strict';

    return originalMessages.extend({
        defaults: {
            hideSpeed: 0,
            hideTimeout: false  // disable auto-close
        },

        /**
         * Override the hide timeout — do nothing
         */
        scheduleHide: function () {
            // intentionally empty — no auto-close
        }
    });
});