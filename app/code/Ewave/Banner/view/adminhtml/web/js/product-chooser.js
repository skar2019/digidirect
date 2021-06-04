define([
    'jquery',
    'Magento_Ui/js/modal/modal-component'
], function ($, Modal) {
    'use strict';

    return Modal.extend({
        defaults: {
            options: {
                chooserGridIdSelector: '#product_chooser tbody  tr',
                targetProductIdSelector: '[name="product_id"]',
                targetProductNameSelector: '[name="product_name"]',
                sourceColNameSelector: '.col-chooser_name',
                sourceColIdSelector: '.col-entity_id'
            }
        },

        /**
         * Open modal
         */
        openModal: function ()
        {
            var self = this;
            self._super();

            $(document).on('click', self.options.chooserGridIdSelector, function (e) {
                var productName = $(this).find(self.options.sourceColNameSelector).text().trim(),
                    productId = $(this).find(self.options.sourceColIdSelector).text().trim();

                $(self.options.targetProductIdSelector).val("product/" + productId).change();
                $(self.options.targetProductNameSelector).val(productName);
                self.closeModal();
            });
        }
    });
});
