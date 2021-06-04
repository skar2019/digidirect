define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/js/catalog-add-to-cart',
    'jquery/ui'
], function ($, modal, $t, customerData) {
    'use strict';
    
    $.widget('ewave.outstockNotification', {
        options: {
            availableProducts: [],
            outofstockHideContainer: '[data-role="outofstock-button"]',
            outofstockformContainer: '[data-role="outofstock-form"]',
            actionContainer: '.product-options-bottom',
            modal: true,
            popupButton: '[data-role="outofstock-popup-show"]',
            visibleClass: '-visible-block',
            modalOptions: {
                type: 'popup',
                title: $t('Notify Me'),
                modalClass: 'outofstock-modal',
                buttons: []
            },
            notificationForBackorder: false,
            isBackorderProduct: false,
            backorderButtonLabel: $t('Back-order'),
            backorderButtonClassname: '-back-order',
            addToCartFormSelector: '#product_addtocart_form',
            addToCartButtonSelector: '.action.tocart',
            emailField: '[data-role="outofstock-email-field"]'
        },

        _isBackOrderEvent: false,
        
        _create: function () {
            var self = this;
            if (this.options.modal) {
                this._createPopup();
                $(this.options.popupButton).on('click', this._openPopup.bind(this));
            }
            if (!this.options.availableProducts.length || this.options.notificationForBackorder) {
                $(this.options.outofstockHideContainer).addClass(this.options.visibleClass);
            }
            this.setBackOrderAddToCart();

            this.checkCutomerEmail();

            $(document).on('ajax:addToCart', function (e, data) {
                if (self._isBackOrderEvent) {
                    self.element.catalogAddToCart('setDefaultOptins', 'addToCartButtonTextDefault', self.options.backorderButtonLabel);
                }
            });

            this.element.on('click', '.' + this.options.backorderButtonClassname, function () {
                self._isBackOrderEvent = true;
            });
        },

        _createPopup: function () {
            modal(this.options.modalOptions, $(this.options.outofstockformContainer));
        },

        _openPopup: function () {
            $(this.options.outofstockformContainer).modal('openModal');
        },

        setBackOrderAddToCart: function () {
            if (this.options.notificationForBackorder && this.options.isBackorderProduct) {
                var $addToCartButton = $(this.options.addToCartFormSelector).find(this.options.addToCartButtonSelector);
                $addToCartButton.find('span').text(this.options.backorderButtonLabel);
                $addToCartButton.attr('title', this.options.backorderButtonLabel);
                $addToCartButton.addClass(this.options.backorderButtonClassname);
            }
        },

        checkCutomerEmail: function () {
            var data = customerData.get('customer')();
            if (data.email) {
                $(this.options.emailField).val(data.email);
            }
        }
    });
    
    return $.ewave.outstockNotification;
});
