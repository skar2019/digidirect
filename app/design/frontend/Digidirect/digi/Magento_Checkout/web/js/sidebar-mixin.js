define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    'Magento_Customer/js/customer-data',
    'customScrollbarInit'
], function($, $t, confirm, customerData) {
    'use strict';

    return function(target) {
        $.widget('mage.sidebar', target, {
            options: {
                minicartScrollWrapperSelector: '.minicart-scroll-wrapper'
            },
            _initContent: function() {
                var self = this,
                    events = {};

                this.element.decorate('list', this.options.isRecursive);

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.button.checkout] = $.proxy(function() {
                    var cart = customerData.get('cart'),
                        customer = customerData.get('customer'),
                        element = $(this.options.button.checkout);

                    if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                        // set URL for redirect on successful login/registration. It's postprocessed on backend.
                        $.cookie('login_redirect', this.options.url.checkout);

                        if (this.options.url.isRedirectRequired) {
                            element.prop('disabled', true);
                            location.href = this.options.url.loginUrl;
                        } else {
                            authenticationPopup.showModal();
                        }

                        return false;
                    }
                    element.prop('disabled', true);
                    location.href = this.options.url.checkout;
                }, this);

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.button.remove] = function(event) {
                    event.stopPropagation();
                    confirm({
                        content: $t('Are you sure you would like to remove this item?'),
                        title: 'Attention',
                        actions: {
                            /** @inheritdoc */
                            confirm: function() {
                                self._removeItem($(event.currentTarget));
                            },

                            /** @inheritdoc */
                            always: function(e) {
                                e.stopImmediatePropagation();
                            }
                        },
                        buttons: [{
                            text: $t('No, Keep It'),
                            class: 'action primary action-dismiss',

                            click: function(event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $t('Yes, Remove It'),
                            class: 'action-primary action-accept',

                            click: function(event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['keyup ' + this.options.item.qty] = function(event) {
                    self._showItemButton($(event.target));
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['change ' + this.options.item.qty] = function(event) {
                    //self._showItemButton($(event.target));
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.item.button] = function(event) {
                    //event.stopPropagation();
                    //console.log(this.options.item.button);
                    //self._updateItemQty($(event.currentTarget));

                    return false;
                };

                /**
                 * @param {jQuery.Event} event
                 * Custom Event (Rondel)
                 */
                events['click ' + ':button.minicart-qty-increase'] = function(event) {
                    event.stopPropagation();
                    self._updateItemQtyIncrease($(event.currentTarget));
                };

                /**
                 * @param {jQuery.Event} event
                 * Custom Event (Rondel)
                 */
                events['click ' + ':button.minicart-qty-decrease'] = function(event) {
                    event.stopPropagation();
                    self._updateItemQtyDecrease($(event.currentTarget));
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['focusout ' + this.options.item.qty] = function(event) {
                    self._validateQty($(event.currentTarget));
                };

                /**
                 * @param {jQuery.Event} event
                 */

                this._on(this.element, events);
                this._calcHeight();
            },

            _updateItemQty: function(elem) {
                //var itemId = elem.data('cart-item');
                //$('#cart-item-' + itemId + '-qty');
                //this._updateItemQtyIncrease(elem);
                // var eventQty = $('#cart-item-' + itemId + '-qty').val();

                // alert(eventQty);


                return false;
            },

            //Rondel Custom Function
            _updateItemQtyIncrease: function(elem) {
                var itemId = elem.data('cart-item');

                this._ajax(this.options.url.update, {
                    'item_id': itemId,
                    'item_qty': Number($('#cart-item-' + itemId + '-qty').val()) + 1
                }, elem, this._updateItemQtyAfter);
            },

            //Rondel Custom Function
            _updateItemQtyDecrease: function(elem) {
                console.log("decrease called!")

                // var itemId = elem.data('cart-item');
                // var currentValue = $('#cart-item-' + itemId + '-qty');

                // if () {

                // }

                // this._ajax(this.options.url.update, {
                //     'item_id': itemId,
                //     'item_qty': Number($('#cart-item-' + itemId + '-qty').val()) - 1
                // }, elem, this._updateItemQtyAfter);
                // }, elem, alert("test"), this._updateItemQtyAfter);
            },

            /**
             * @param {HTMLElement} elem
             * @private
             */
            _hideItemButton: function(elem) {
                return false;
            },

            _calcHeight: function() {
                $(this.options.minicart.list).parents(this.options.minicartScrollWrapperSelector).trigger('updateHeight');
            }
        });

        return $.mage.sidebar;

    };
});