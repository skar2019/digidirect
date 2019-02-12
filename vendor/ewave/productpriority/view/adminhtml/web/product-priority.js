define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';
    $.widget('ewave.productPriority', {
        options: {
            reCalculateAjaxUrl: null,
            form_key: null
        },
        _create: function () {
            var $this = this;
            $('#recalculate').on('click', function (e) {
                window.location.href = $this.options.reCalculateAjaxUrl;
            });
            $('#product_priority_cron_settings_cron_value').on('change', function (e) {
                if (this.value != 0) {
                    var cronTime = $('#product_priority_cron_settings_cron_time');
                    cronTime[0].value = this.value;
                }
            });
            $('#product_priority_cron_settings_cron_time').on('keyup', function (e) {
                $this._checkCronSelect(this.value);

            });
            this._checkCronSelect($('ewave_productpriority_config_cron_time').value);
        },
        _checkCronSelect: function (v) {
            var sel = $('#product_priority_cron_settings_cron_value');
            if (!sel.length) {
                return;
            }
            var opts = sel[0].options;
            for (var opt, j = 0; opt = opts[j]; j++) {
                if (opt.value == v) {
                    sel[0].selectedIndex = j;
                    break;
                } else {
                    sel[0].selectedIndex = 0;
                }
            }
        }
    });
    return $.ewave.productPriority;
});
