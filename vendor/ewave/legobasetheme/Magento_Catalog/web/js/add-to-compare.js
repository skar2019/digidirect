define([
    'jquery',
    'jquery/ui',
    'mage/dataPost'
], function ($) {
    'use strict';

    $.widget('ewave.addToCompare', {
        options: {
            addToCompare: '.action.tocompare'
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var self = this;
            $(document).on('click', this.options.addToCompare, function (e) {
                e.preventDefault();
                self._sendRequest($(this).data('post'), $(this));
            });
        },
        _sendRequest: function (params, element) {
            var self = this;
            this._extendParams(params);
            $.ajax({
                method: 'POST',
                url: params.action,
                showLoader: true,
                data: params.data,
                success: function (data) {
                    self.success(data, element);
                },
                error: function () {
                    self.error();
                }
            });
        },
        _extendParams: function (params) {
            var formKey = $($('body').dataPost('option', 'formKeyInputSelector')).val();
            params.data['isAjax'] = true;
            if (formKey) {
                params.data['form_key'] = formKey;
            }
        },
        success: function (data, element) {
        },
        error: function () {
        }
    });

    return $.ewave.addToCompare;
});
