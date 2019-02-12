define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    $.widget('ewave.confirmationPopup', {
        options: {
            continueButton: '[data-role="continue-button"]',
            modalConfig: {
                modalClass: 'confirmation-popup',
                buttons: []
            }
        },

        _create: function () {
            this.initModal();
            this._bind();
        },

        _bind: function () {
            this.element.on('click', this.options.continueButton, $.proxy(this.closeModal, this));
        },

        /**
         * Init modal
         */
        initModal: function () {
            this.element.modal(this.options.modalConfig);
            this.openModal();
        },

        /**
         * Open modal
         */
        openModal: function () {
            this.element.modal('openModal');
        },

        /**
         * Close modal
         */
        closeModal: function () {
            this.element.modal('closeModal');
        }
    });

    return $.ewave.confirmationPopup;
});
