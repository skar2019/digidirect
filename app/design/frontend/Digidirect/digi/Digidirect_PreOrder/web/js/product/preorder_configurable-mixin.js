define([
    'jquery',
    'jquery/ui',
    'preOrder'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('digidirect.preOrderConfigurable', target, {
            _original: {
                availabilityText: '',
                addToCartLabel: ''
            }
        });

        return $.digidirect.preOrderConfigurable;
    };
});
