define([
    'jquery',
    'matchMedia',
    'mage/template',
    'jquery/ui',
    'mage/dataPost',
    'customScrollbarInit'
], function ($, mediaCheck, mageTemplate ) {
    'use strict';

    return function (target) {
        $.widget('ewave.quickViewPageEvents', target, {
            _create: function () {
                $('html').addClass('full-height');
                $('body').dataPost('disable');
                $('body').find('.columns').customScrollbar();
                this._bind();
            }
        });

        return $.ewave.quickViewPageEvents;
    };
});