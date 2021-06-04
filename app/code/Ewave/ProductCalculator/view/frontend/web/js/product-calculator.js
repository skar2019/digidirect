define([
    'jquery',
    'jquery/ui',
    'mage/validation'
], function ($) {
    'use strict';

    $.widget('ewave.productCalculator', {
        options: {
            redirectToResult: false,
            resultContainer: '.calculator-result',
            loadedSelector: '-loaded'
        },
        _create: function () {
            this._bind();
        },
        _bind: function () {
            var self = this;
            this.element.on('submit', function (e) {
                e.preventDefault();
                var $this = $(this);
                if ($this.valid()) {
                    self.sendRequest($this);
                }
            });
        },
        sendRequest: function ($form) {
            var
                self = this,
                requestDelimiter = $form.attr('action').indexOf('?') === -1 ? '?' : '&';
            if (self.options.redirectToResult) {
                document.location.href = $form.attr('action') + requestDelimiter + $form.serialize();
            } else {
                $.ajax({
                    method: 'POST',
                    url: $form.attr('action'),
                    showLoader: true,
                    data: $form.serialize(),
                    success: function (data) {
                        self.success(data);
                    },
                    error: function () {
                        self.error();
                    }
                });
            }
        },
        success: function (data) {
            var $resultContainer = $(this.options.resultContainer);
            $resultContainer.addClass(this.options.loadedSelector).html(data);
            $resultContainer.trigger('contentUpdated');
        },
        error: function () {
            $(this.options.resultContainer).removeClass(this.options.loadedSelector);
        }
    });

    return $.ewave.productCalculator;
});
