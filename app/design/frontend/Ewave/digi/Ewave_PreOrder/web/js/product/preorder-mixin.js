define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.preOrder', target, {
            _changeLabels: function () {
                var $toCartBlock = $('.product-info-main .box-tocart');

                if ($toCartBlock.length) {
                    if (this.options.preOrderNote) {
                        $('<div class="pdp-preorder">' + this.options.preOrderNote + '</div>').prependTo($toCartBlock);
                    }

                    this._setButtonLabel(this.options.addToCartLabel);
                } else {
                    this._super();
                }
            }
        });

        return $.ewave.preOrder;
    };
});