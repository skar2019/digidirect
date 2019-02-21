define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.productCalculator', target, {
            success: function (data) {
                this._super(data);
                $(document).trigger('finder.results.success', [$(this.options.resultContainer), data]);
            },
            error: function () {
                this._super();
                $(document).trigger('finder.results.error', [$(this.options.resultContainer)]);
            }
        });

        return $.ewave.productCalculator;
    }
});
