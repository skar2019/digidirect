define([
    'jquery',
    'jquery/ui',
    'catalogAddToCart'
], function ($) {
    'use strict';

    $.widget('digidirect.preOrder', {
        options: {
            autoEnable: false,
            availabilityElement: $('.catalog-product-view .stock').first(),
            addToCartButton: $('#product-addtocart-button'),
            preOrderNote: '',
            addToCartLabel: '',
            addToCartForm: '',
            preOrderButtonClass: '-pre-order'
        },

        _original: {
            availabilityText: '',
            addToCartLabel: ''
        },

        _enabled: false,
        _isPreOrderEvent: false,
        _addToCartWidgetIsInited: false,

        _create: function () {
            this.isAvailability = false;
            if (this.options.availabilityElement.length) {
                this.isAvailability = true;
            }
            this.options.addToCartForm = this.options.addToCartForm ? $(this.options.addToCartForm) : this.element.closest('[data-role="tocart-form"]');
            this._saveOriginal();
            if (this.options.autoEnable) {
                this.enable();
            }
            $(document).on('ajax:addToCart', $.proxy(function () {
                this._setDefaultLabel();
            }, this));
        },

        _saveOriginal: function () {
            if (!this.options.addToCartButton) {
                return;
            }
            if (this.isAvailability) {
                this._original.availabilityText = this.options.availabilityElement.html();
            }
            this._original.addToCartLabel = this.options.addToCartButton.html();
        },

        _changeLabels: function () {
            if (this.isAvailability) {
                this.options.availabilityElement.html(this.options.preOrderNote);
            }
            this._setButtonLabel(this.options.addToCartLabel);
        },

        _setButtonLabel: function (label) {
            var innerSelector = this.options.addToCartButton.find('span');
            this.options.addToCartButton.attr('title', $('<div>' + label + '</div>').text());
            if (innerSelector.length) {
                innerSelector.html(label);
            } else {
                this.options.addToCartButton.html(label);
            }
            this.isPreOrderLabel = label !== this._original.addToCartLabel;
            if (!this.isPreOrderLabel && this._addToCartWidgetIsInited) {
                this._setDefaultLabel(true);
            }
        },

        enable: function () {
            var self = this;
            this._enabled = true;
            this._changeLabels();
            this.options.addToCartButton.addClass(this.options.preOrderButtonClass);
            this.options.addToCartForm.on('click', '.' + this.options.preOrderButtonClass, function () {
                self._isPreOrderEvent = true;
            });
        },

        disable: function () {
            this._enabled = false;
            if (this.isAvailability) {
                this.options.availabilityElement.html(this._original.availabilityText);
            }
            this._setButtonLabel(this._original.addToCartLabel);
            this.options.addToCartForm.off('click', '.' + this.options.preOrderButtonClass);
            this.options.addToCartButton.removeClass(this.options.preOrderButtonClass);
            if (this._addToCartWidgetIsInited) {
                this._setDefaultLabel(true);
            }
        },

        _setDefaultLabel: function (toClear) {
            if (this._isPreOrderEvent || toClear) {
                this.options.addToCartForm.catalogAddToCart('setDefaultOptins', 'addToCartButtonTextDefault', this.isPreOrderLabel ? this.options.addToCartLabel : null);
            }
            this._isPreOrderEvent = false;
            this._addToCartWidgetIsInited = true;
        }
    });

    return $.digidirect.preOrder;
});
