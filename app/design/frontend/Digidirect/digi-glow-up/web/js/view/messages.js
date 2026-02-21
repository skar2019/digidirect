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

            this.messages = customerData.get('messages').extend({
                disposableCustomerData: 'messages'
            });

            // Force to clean obsolete messages
            if (!_.isEmpty(this.messages().messages)) {
                customerData.set('messages', {});
            }

            $.mage.cookies.set('mage-messages', '', {
                samesite: 'strict',
                domain: ''
            });
        },

        /**
         * Remove a customer data message by index
         */
        removeMessage: function (index) {
            var data = this.messages();

            if (data && data.messages) {
                data.messages.splice(index, 1);
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