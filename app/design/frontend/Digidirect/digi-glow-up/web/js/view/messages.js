define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'escaper',
    'ko',
    'jquery/jquery-storageapi'
], function ($, Component, customerData, _, escaper, ko) {
    'use strict';
    return Component.extend({
        defaults: {
            cookieMessages: [],
            messages: [],
            allowedTags: ['div', 'span', 'b', 'strong', 'i', 'em', 'u', 'a']
        },
        initialize: function () {
            this._super();
            this.cookieMessages = ko.observableArray(
                _.unique($.cookieStorage.get('mage-messages'), 'text')
            );
            this.messages = customerData.get('messages');

            var self = this;
            var captured = false;

            this.messages.subscribe(function (newValue) {
                if (newValue && newValue.messages && newValue.messages.length > 0) {
                    self._persistedMessages = newValue;
                    captured = true;
                } else if (captured && self._persistedMessages) {
                    customerData.set('messages', self._persistedMessages);
                }
            });

            $.mage.cookies.set('mage-messages', '', {
                samesite: 'strict',
                domain: ''
            });
        },
        /**
         * Add extra class name to message element
         */
        addExtraClassName: function (element) {
            var className = $(element).find('[data-message-classname]').data('message-classname');
            if (className) {
                $(element).closest('.message').addClass(className);
            }
        },
        /**
         * Remove a customer data message by index
         */
        removeMessage: function (index) {
            var data = this._persistedMessages || this.messages();
            if (data && data.messages) {
                data.messages.splice(index, 1);
                if (data.messages.length === 0) {
                    this._persistedMessages = null;
                }
                customerData.set('messages', data);
            }
        },
        /**
         * Remove a cookie message by index
         */
        removeCookieMessage: function (index) {
            this.cookieMessages.splice(index, 1);
        },
        /**
         * Prepare the given message to be rendered as HTML
         */
        prepareMessageForHtml: function (message) {
            return escaper.escapeHtml(message, this.allowedTags);
        }
    });
});