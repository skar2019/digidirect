define([
    'ko',
    'underscore',
    'uiRegistry',
    'jquery'
], function (ko, _, uiRegistry, $) {
    'use strict';

    return function (target) {
        return target.extend({
            defaults: {
                shippingAddressUiRegistryName: 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset',
                toggleExtraFields: {}
            },

            initialize: function () {
                this._super();

                this.setCollectAbstractEntityFields();
            },

            toggleCollectType: function (data, e) {
                this._super(data, e);

                if (this.isSingleCartCollectVariation()) {
                    if ($(e.currentTarget).val() === 'collect') {
                        this.enableCollectMode();
                        this.toggleShippingAddressFields(false);
                    } else {
                        this.disableCollectMode();
                        this.toggleShippingAddressFields(true);
                    }
                }
            },

            toggleShippingAddressFields: function (isVisible) {
                var self = this,
                    fieldsData = this.getMappedAbstractEntityData();

                _.each(fieldsData, function (item, key) {
                    if (key === 'street' || (typeof item === 'object' && !_.isNull(item))) {
                        uiRegistry.async(self.shippingAddressUiRegistryName + '.' + key)(function (group) {
                            if (group) {
                                if (typeof item !== 'object') {
                                    item = [item];
                                }
                                _.each(item, function (row, i) {
                                    uiRegistry.async(self.shippingAddressUiRegistryName + '.' + key + '.' + i)(function (field) {
                                        self.updateShippingAddressField(field, row, isVisible);
                                    });
                                });
                                group.visible(isVisible);
                            }
                        });
                    } else {
                        uiRegistry.async(self.shippingAddressUiRegistryName + '.' + key)(function (field) {
                            if (field) {
                                self.updateShippingAddressField(field, item, isVisible);
                            }
                        });
                    }
                });

                this.toggleRegionFields(fieldsData, isVisible);
            },

            updateShippingAddressField: function (field, val, isVisible) {
                if (isVisible) {
                    field.reset();
                } else {
                    field.value(val);
                }

                field.setVisible(isVisible);
            },

            getMappedAbstractEntityData: function () {
                var abstractFields,
                    preFillShippingFields = window.checkoutConfig.quoteData.collect_prefill_shipping_fields;

                if (this.collectPlaces() && this.collectPlaces().length) {
                    abstractFields = this.collectPlaces()[0].collect_prefill_shipping_fields ? this.collectPlaces()[0].collect_prefill_shipping_fields : {};
                } else {
                    abstractFields = preFillShippingFields ? preFillShippingFields.reduce(function (acc, cur) {
                        acc[cur] = '';
                        return acc;
                    }, {}) : {};
                }

                return _.extend({}, abstractFields, this.toggleExtraFields);
            },

            setCollectAbstractEntityFields: function () {
                var self = this;

                if (this.isSingleCartCollectVariation() && this.collectPlaces() && this.collectPlaces().length) {
                    self.toggleShippingAddressFields(false);
                    self.enableCollectMode();
                }
            },

            selectCollectShippingMethod: function () {
                $('input[value="collect_collect"]').trigger('click');
            },

            enableCollectMode: function () {
                this.selectCollectShippingMethod();
                $('.opc-wrapper').addClass('-hide-methods');
            },

            disableCollectMode: function () {
                $('.opc-wrapper').removeClass('-hide-methods');
            },

            onSuccessApplyPlace: function (response) {
                this._super(response);

                if (this.isSingleCartCollectVariation()) {
                    this.toggleShippingAddressFields(false);
                }
            },

            toggleRegionFields: function (fieldsData, isVisible) {
                var country,
                    result,
                    regionId,
                    regionIdInput;

                if (typeof fieldsData['region_id'] !== 'undefined') {
                    regionId = uiRegistry.get(this.shippingAddressUiRegistryName + '.region_id');
                    regionIdInput = uiRegistry.get(this.shippingAddressUiRegistryName + '.region_id_input');
                    if (regionId) {
                        if (isVisible) {
                            country = uiRegistry.get(this.shippingAddressUiRegistryName + '.country_id');
                            if (country) {
                                result = _.filter(regionId.initialOptions, function (item) {
                                    return item.country_id === country.value();
                                });
                            }
                            if (result && result.length) {
                                regionId.setVisible(isVisible);
                                if (regionIdInput) {
                                    regionIdInput.setVisible(!isVisible);
                                }
                            } else {
                                regionId.setVisible(!isVisible);
                                if (regionIdInput) {
                                    regionIdInput.setVisible(isVisible);
                                }
                            }
                        } else {
                            regionId.setVisible(isVisible);
                            if (regionIdInput) {
                                regionIdInput.setVisible(isVisible);
                            }
                        }
                    }
                }
            }
        });
    };
});
