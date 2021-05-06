define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('digidirect.preOrderGrouped', target, {
            _create: function () {
                this._super();
                this._checkLabelAfterCreation();
            },
            _checkLabelAfterCreation: function () {
                var self = this;
                $.each(this.options.map, function (key, value) {
                    var $currentInput = $('.qty input[name=super_group\\[' + key + '\\]]').first();

                    if ($currentInput.length && $currentInput.val() > 0) {
                        self.options.addToCartLabel = value.cartLabel;
                        self.options.preOrderNote = value.note;
                        self.enable();
                    } else {
                        self.disable();
                    }
                });
            }
        });

        return $.digidirect.preOrderGrouped;
    };
});
