define([
  'jquery',
  'underscore',
  'Digidirect_MyStoreWidget/js/dist/common/component',
  'jquery/ui',
  'mage/translate',
  'mage/validation'
], function ($, _, Component) {
  'use strict';
  return function (widget) {
    $.widget('digidirect.collectPlacesAvailability', widget, {

      _bind: function () {
        this.element.on('updateSwatches', this.sendRequest.bind(this));
      },
    });
    return $.digidirect.collectPlacesAvailability;
  }
});
