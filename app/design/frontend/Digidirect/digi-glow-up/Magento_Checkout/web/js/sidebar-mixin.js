define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict'

    return function (originalWidget) {
        // Create a new widget that extends the original one
        $.widget('mage.sidebar', originalWidget, {
            /**
             * Override the _create method to fix the item.qty and item.button errors
             */
            _create: function () {
                var self = this,
                    events = {};

                // Fix the events binding with optional chaining checks
                if (this.options.item?.qty) {
                    /**
                     * @param {jQuery.Event} event
                     */
                    events['keyup ' + this.options.item.qty] = function (event) {
                        self._showItemButton($(event.target));
                    };

                    /**
                     * @param {jQuery.Event} event
                     */
                    events['change ' + this.options.item.qty] = function (event) {
                        self._showItemButton($(event.target));
                    };

                    /**
                     * @param {jQuery.Event} event
                     */
                    events['focusout ' + this.options.item.qty] = function (event) {
                        self._validateQty($(event.currentTarget));
                    };
                }

                if (this.options.item?.button) {
                    /**
                     * @param {jQuery.Event} event
                     */
                    events['click ' + this.options.item.button] = function (event) {
                        event.stopPropagation();
                        self._updateItemQty($(event.currentTarget));
                    };
                }

                // Call the parent _create and merge our fixed events
                this._super();

                // Rebind the fixed events
                this._on(this.element, events);
            },

            _removeItem: function (id) {
                var self = this

                confirm({
                    content: $.mage.__('Are you sure you would like to remove this item from your cart?'),
                    buttons: [
                        {
                            text: $.mage.__('No, keep it'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event)
                            }
                        },
                        {
                            text: $.mage.__('Yes, remove it'),
                            class: 'action-primary action-accept',
                            click: function () {
                                self._super(id) // call original remove
                                this.closeModal(true)
                            }
                        }
                    ]
                })
            }
        })

        return $.mage.sidebar
    }
})
