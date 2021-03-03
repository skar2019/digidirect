define([
    'uiComponent',
    'jquery',
     'Magento_Customer/js/customer-data'
    ], function (Component, $, customerData) {
      'use strict';
      return Component.extend({
        initialize: function (config, node) {
          this._super();
          this.removeUrl = config.removeUrl;
          $(node).click(function (e) {
            e.preventDefault();
            this.removeGiftCard();
          }.bind(this))
        },
        removeGiftCard: function () {
          var cartData = customerData.get('cart')();
          cartData['data_id'] = 0;
          customerData.set('cart', cartData);
          location.href = this.removeUrl;
        }
      });
    });