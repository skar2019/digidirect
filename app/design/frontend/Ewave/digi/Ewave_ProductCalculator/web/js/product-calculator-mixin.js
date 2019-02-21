define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.productCalculator', target, {
            success: function (data) {
                this._super(data);
                $('#' + $(this.options.resultContainer).data('role')).trigger('finder.results.success', [$(this.options.resultContainer), data]);
            },
            error: function () {
                this._super();
                $('#' + $(this.options.resultContainer).data('role')).trigger('finder.results.error', [$(this.options.resultContainer)]);
            }
        });

        return $.ewave.productCalculator;
    }
});
