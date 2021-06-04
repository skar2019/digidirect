define([
    'jquery',
    'mage/storage',
    'Magento_Checkout/js/model/url-builder',
    'jquery/ui'
], function ($, storage, urlBuilder) {
    'use strict';

    return function (widget) {
        $.widget('ewave.shippingAvailability', widget, {
            options: {
                usePostcodeZone: true
            },

            lastResponseData: null,

            _create: function () {
                this._super();
            },

            checkMethods: function (zone) {
                var postcode = $(this.options.postcode).val();
                if (this.options.usePostcodeZone && postcode && !zone) {
                    this.getZone(postcode);
                } else {
                    this._super();
                }
            },

            /**
             * Get address zone via postcode
             */
            getZone: function (postcode) {
                var self = this,
                    serviceUrl = this.options.customerId ? urlBuilder.createUrl('/shippingavailability/getZoneByPostcode', {}) : urlBuilder.createUrl('/shippingavailability/guest/getZoneByPostcode', {});

                serviceUrl += '/?postcode=' + postcode;
                this.startProcess();

                storage.get(
                    serviceUrl
                ).done(function (response) {
                    self.lastResponseData = response;
                    self.changeCountryField();
                }).fail(function (response) {
                    var error = JSON.parse(response.responseText);
                    if (error && error.message) {
                        self.messageContainer = {
                            messages: [{
                                type: 'error',
                                text: error.message
                            }]
                        };
                        self.setErrorMessage();
                        self.clearShippingMethodsList();
                    }
                    self.stopProcess();
                });
            },

            /**
             * Callback after update of region
             */
            regionUpdated: function () {
                this._super();
                if (this.lastResponseData) {
                    this.changeRegionField();
                }
            },

            /**
             * Change country field
             */
            changeCountryField: function () {
                if (this.lastResponseData && this.lastResponseData.country_id) {
                    $(this.options.countryId).val(this.lastResponseData.country_id).trigger('change');
                } else {
                    this._checkMethods();
                }
            },

            /**
             * Change region fieled
             */
            changeRegionField: function () {
                if (+this.lastResponseData.region_id !== 0) {
                    $(this.options.regionId).val(this.lastResponseData.region_id);
                }
                this._checkMethods();
            },

            /**
             * Run check shipping methods
             * @private
             */
            _checkMethods: function () {
                this.stopProcess();
                this.checkMethods(true);
                this.clearLastResponseData();
            },

            /**
             * Clear data
             */
            clearLastResponseData: function () {
                this.lastResponseData = null;
            }

        });

        return $.ewave.shippingAvailability;
    };
});
