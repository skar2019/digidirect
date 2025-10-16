define([
  'jquery',
  'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
  'use strict';

  return function (target) {
    return $.extend(target, {
      _removeItem: function (id) {
        const self = this;

        confirm({
          content: $.mage.__('Are you sure you would like to remove this item?'),
          buttons: [
            {
              text: $.mage.__('No, Keep it'),
              class: 'action-secondary action-dismiss',
              click: function (event) {
                this.closeModal(event);
              }
            },
            {
              text: $.mage.__('Yes, Remove it'),
              class: 'action-primary action-accept',
              click: function () {
                self._removeItemAfterConfirm(id);
                this.closeModal(true);
              }
            }
          ]
        });
      },

      _removeItemAfterConfirm: function (id) {
        // call the original removal logic
        this._super(id);
      }
    });
  };
});
