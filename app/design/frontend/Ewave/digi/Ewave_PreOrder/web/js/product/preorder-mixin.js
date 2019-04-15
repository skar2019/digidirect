define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return function (target) {
        $.widget('ewave.preOrder', target, {
            options: {
                preOrderNoteClass: 'pdp-preorder',
                preOrderNoteSelector: '.pdp-preorder',
                toCartBlockSelector: '.product-info-main .box-tocart',
            },
            _create: function() {
                this.options.addToCartForm = this.options.addToCartForm ? $(this.options.addToCartForm) : this.element.closest('[data-role="tocart-form"]');

                this._super();
            },
            disable: function() {
              this._super();
              this._removeOldLabels();
            },
            _changeLabels: function () {
                var $toCartBlock = $(this.options.toCartBlockSelector);

                if ($toCartBlock.length) {
                    if (this.options.preOrderNote) {
                        this._removeOldLabels($toCartBlock);
                        $('<div>', {
                            class: this.options.preOrderNoteClass,
                            text: this.options.preOrderNote,
                        }).prependTo($toCartBlock);
                    }

                    this._setButtonLabel(this.options.addToCartLabel);
                } else {
                    this._super();
                }
            },
            _setDefaultLabel: function() {
              this._super();
              this._removeOldLabels();
            },
            _removeOldLabels: function($parentElement) {
                if (!$parentElement || $parentElement.length) {
                    $parentElement = $(this.options.toCartBlockSelector);
                }
                $parentElement.find(this.options.preOrderNoteSelector).remove();
            },
        });

        return $.ewave.preOrder;
    };
});
