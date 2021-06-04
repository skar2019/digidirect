define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.dataPost', widget, {
            options: {
                toCompareClassName: 'tocompare'
            },
            _postDataAction: function (e) {
                if (!$(e.currentTarget).hasClass(this.options.toCompareClassName)) {
                    this._super(e);
                }
            }
        });

        return $.mage.dataPost;
    };
});
