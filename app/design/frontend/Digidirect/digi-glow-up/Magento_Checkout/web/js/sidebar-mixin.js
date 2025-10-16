define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    'use strict'

    return function (Component) {
        return Component.extend({
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
                                self._super(id) // call original remove logic
                                this.closeModal(true)
                            }
                        }
                    ]
                })
            }
        })
    }
})
