define([
    'jquery',
    'jquery/ui',
    'catalogAddToCart'
], function ($) {
    'use strict';

    $.widget('ewave.preOrder', {
        options: {
            autoEnable: false,
            availabilityElement: $('.catalog-product-view .stock').first(),
            addToCartButton: $('#product-addtocart-button'),
            preOrderNote: '',
            addToCartLabel: '',
            addToCartForm: ''
        },

        _original: {
            availabilityText: '',
            addToCartLabel: ''
        },

        _enabled: false,

        _create: function () {
            this.isAvailability = false;
            if (this.options.availabilityElement.length) {
                this.isAvailability = true;
            }
            this._saveOriginal();

            if (this.options.autoEnable) {
                this.enable();
            }
            this.options.addToCartForm = this.options.addToCartForm ? $(this.options.addToCartForm) : this.element.closest('[data-role="tocart-form"]');
            this.options.addToCartButton.on('click', $.proxy(this._setDefaultLabel, this));
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
        },

        enable: function () {
            this._enabled = true;
            this._changeLabels();
        },

        disable: function () {
            this._enabled = false;
            if (this.isAvailability) {
                this.options.availabilityElement.html(this._original.availabilityText);
            }
            this._setButtonLabel(this._original.addToCartLabel);
            this._setDefaultLabel(null, this._original.addToCartLabel);
        },

        _setDefaultLabel: function (e, data) {
            this.options.addToCartForm.catalogAddToCart('setDefaultOptins', 'addToCartButtonTextDefault', data || this.options.addToCartLabel);
        }
    });

    return $.ewave.preOrder;
});
