/* global location */
define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'jquery/ui'
], function ($, authenticationPopup, customerData, alert, confirm) {
    'use strict';

    return function (widget) {
        $.widget('mage.sidebar', widget, {
            _initContent: function () {
                var self = this,
                    events = {};

                this.element.decorate('list', this.options.isRecursive);

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.button.close] = function (event) {
                    event.stopPropagation();
                    $(self.options.targetElement).dropdownDialog('close');
                };
                events['click ' + this.options.button.checkout] = $.proxy(function (e) {
                    var cart = customerData.get('cart'),
                        customer = customerData.get('customer'),
                        element = e.currentTarget;

                    e.preventDefault();

                    $(document).trigger('click:proceedToCheckout', [this.options, element]);

                    if ($(element).data('stop')) {
                        authenticationPopup.showModal();
                        return false;
                    }

                    if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                        // set URL for redirect on successful login/registration. It's postprocessed on backend.
                        $.cookie('login_redirect', this.options.url.checkout);

                        if (this.options.url.isRedirectRequired) {
                            location.href = this.options.url.loginUrl;
                        } else {
                            authenticationPopup.showModal();
                        }

                        return false;
                    }
                    location.href = this.options.url.checkout;
                }, this);

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.button.remove] = function (event) {
                    event.stopPropagation();
                    confirm({
                        content: self.options.confirmMessage,
                        actions: {
                            /** @inheritdoc */
                            confirm: function () {
                                self._removeItem($(event.currentTarget));
                            },

                            /** @inheritdoc */
                            always: function (e) {
                                e.stopImmediatePropagation();
                            }
                        }
                    });
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['keyup ' + this.options.item.qty] = function (event) {
                    self._showItemButton($(event.target));
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['click ' + this.options.item.button] = function (event) {
                    event.stopPropagation();
                    self._updateItemQty($(event.currentTarget));
                };

                /**
                 * @param {jQuery.Event} event
                 */
                events['focusout ' + this.options.item.qty] = function (event) {
                    self._validateQty($(event.currentTarget));
                };

                this._on(this.element, events);
                this._calcHeight();
                this._isOverflowed();
            }
        });
        return $.mage.sidebar;
    };
});
