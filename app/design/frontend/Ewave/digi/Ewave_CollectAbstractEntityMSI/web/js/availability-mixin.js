define([
  'jquery',
  'underscore',
  'Ewave_MyStoreWidget/js/dist/common/component',
  'jquery/ui',
  'mage/translate',
  'mage/validation'
], function ($, _, Component) {
  'use strict';
  return function (widget) {
    $.widget('ewave.collectPlacesAvailability', widget, {

      _bind: function () {
        this.element.on('updateSwatches', this.sendRequest.bind(this));
      },
    });
    return $.ewave.collectPlacesAvailability;
  }
});
