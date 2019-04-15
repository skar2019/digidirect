define([
    'jquery',
    'jquery/ui',
    'preOrder'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.preOrderConfigurable', target, {
            _original: {
                availabilityText: '',
                addToCartLabel: ''
            }
        });

        return $.ewave.preOrderConfigurable;
    };
});
