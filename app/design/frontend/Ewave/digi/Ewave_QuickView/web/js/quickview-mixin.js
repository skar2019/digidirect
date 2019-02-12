define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.quickView', target, {
            options: {
                modal: {
                    type: 'popup',
                    buttons: [],
                    modalClass: 'quickView-modal'
                }
            }
        });

        return $.ewave.quickView;
    };

});
