define([
    'jquery',
    'mage/template',
    'jquery/ui',
    'mage/dataPost',
    'customScrollbarInit'
], function ($, mageTemplate) {
    'use strict';

    return function (target) {
        $.widget('ewave.quickViewPageEvents', target, {
            _create: function () {
                $('html').addClass('full-height');
                $('body').dataPost('disable').find('.columns').customScrollbar();
                this._bind();
            }
        });

        return $.ewave.quickViewPageEvents;
    };
});
