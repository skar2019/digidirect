define([
  'jquery',
  'Magento_Checkout/js/view/minicart'
], function ($, Component) {
  'use strict';

  return function (Target) {
    return Target.extend({
      defaults: {
        template: 'Magento_Checkout/minicart/content'
      }
    });
  };
});
