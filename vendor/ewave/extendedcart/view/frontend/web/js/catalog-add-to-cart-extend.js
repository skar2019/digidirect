define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data',
    'mage/template',
    'text!Ewave_ExtendedCart/template/wrapper.html',
    'jquery/ui',
    'confirmationPopup',
    'mage/mage'
], function ($, _, customerData, template, templateWrapper) {
    'use strict';

    return function (widget) {
        $.widget('mage.catalogAddToCart', widget, {
            options: {
                confirmationSelector: '[data-role="confirmation-popup"]',
                containerSelector: 'body'
            },

            onSuccessAjaxSubmit: function (res) {
                this._super(res);
                this.checkResponse(res);
            },

            /**
             * Check response
             * @param {array || object} res
             */
            checkResponse: function (res) {
                if (this.isPopupElement()) {
                    this.enableAddToCartButton(this.element);
                    customerData.reload('cart');
                } else {
                    if (!_.isEmpty(res) && res.confirmation_popup) {
                        this.insertResponseData(res.confirmation_popup);
                    }
                }
            },

            /**
             * Check add to cart from popup
             */
            isPopupElement: function () {
                return this.element.closest(this.options.confirmationSelector).length;
            },

            /**
             * Insert response to target element
             * @param {string} data
             */
            insertResponseData: function (data) {
                var wrapper = $(this.options.confirmationSelector);

                if (!wrapper.length) {
                    $(this.options.containerSelector).append($(template(templateWrapper, {})));
                    wrapper = $(this.options.confirmationSelector);
                    wrapper.html(data);
                    // trigger 'contentUpdated' doesn't work for magento cloud
                    $.mage.init();
                } else {
                    wrapper.html(data);
                    wrapper.confirmationPopup('openModal');
                }
            },

            backUrlRedirect: function (res) {
                if (!this.isPopupElement()) {
                    this._super(res);
                }
            }
        });

        return $.mage.catalogAddToCart;
    };
});
