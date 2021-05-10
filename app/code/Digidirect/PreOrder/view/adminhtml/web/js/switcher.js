define([
    'jquery',
    'underscore',
    'uiComponent',
    'uiRegistry',
    'moment',
    'Magento_Ui/js/lib/validation/validator',
    'mage/translate'
], function ($, _, Component, registry, moment, validator) {
    'use strict';

    return Component.extend({
        defaults: {
            modalComponent: 'product_form.product_form.advanced_inventory_modal',
            influencingFields: ['product[stock_data][backorders]'],
            targetFields: ['product_form.product_form.advanced_inventory_modal.stock_data.container_digidirect_product_availability_date'],
            conditions: [
                {
                    'product[stock_data][backorders]': ['111']
                }     
            ],
            dateField: 'product_form.product_form.advanced_inventory_modal.stock_data.container_digidirect_product_availability_date.digidirect_product_availability_date'
        },
        initialize: function () {
            this._super();
            this.bindOpenModal();
        },

        /**
         * Bind open modal
         */
        bindOpenModal: function () {
            var self = this;
            registry.get(this.modalComponent, function (modal) {
                var stateSubscription = modal.state.subscribe(function (isOpen) {
                    if (isOpen) {
                        /* wait load modal data */
                        setTimeout(function () {
                            self.bindModalProductFormChanges();
                            self.checkState();
                            self.checkDateField();
                        }, 500);
                        stateSubscription.dispose();
                    }
                });
            });
        },

        /**
         * Bind changes in modal
         */
        bindModalProductFormChanges: function () {
            var self = this;
            this.influencingFields.forEach(function (name) {
                $(document).on('change', '[name="' + name + '"]', $.proxy(self.checkState, self));
            });
        },

        /**
         * Check state of fields
         */
        checkState: function () {
            var isVisible = this.isVisisble();
            this.toggleVisibility(isVisible);
            if (this.dateFieldData) {
                if (isVisible) {
                    this.enableDateField(this.dateFieldData);
                } else {
                    this.disableDateField(this.dateFieldData);
                }
                this.resetField(this.dateFieldData);
            }
        },

        /**
         * Toggle visibility
         * @param {boolean} isVisible
         */
        toggleVisibility: function (isVisible) {
            this.targetFields.forEach(function (name) {
                registry.get(name, function (field) {
                    field.visible(isVisible);
                });
            });
        },

        /**
         * Check visibility
         * @returns {boolean}
         */
        isVisisble: function () {
            var self = this,
                result = false;

            this.conditions.forEach(function (condition) {
                if (self.isCoincided(condition)) {
                    result = true;
                }
            });
            return result;
        },

        /**
         * Check conditions
         * @param {array} keys
         * @param {object} condition
         * @returns {boolean}
         */
        isCoincided: function (condition) {
            var self = this,
                result = _.map(_.keys(condition), function (name) {
                    return _.contains(condition[name], self.getValue(name));
                });
            return !_.contains(result, false);
        },

        /**
         * Get value
         * @param {string} name
         * @returns {string}
         */
        getValue: function (name) {
            return $('[name="' + name + '"]').val();
        },

        /**
         * Check date field
         */
        checkDateField: function () {
            var self = this;
            registry.get(this.dateField, function (field) {
                if (field && field.name === self.dateField) {
                    self.dateFieldData = field;
                    self.setAditionalRule();
                    self.setAditionalValidation(field);
                    self.resetField(field);
                }
            });
        },

        /**
         * Set additional rule for validation dte field
         */
        setAditionalRule: function () {
            var handler = function (value, minValue, params) {
                    return value === '' || moment.utc(value, params.dateFormat).unix() >= minValue;
                },
                message = $.mage.__('Please enter a valid date or clear the field.');
            validator.addRule('current_date_or_zero', handler, message);
        },

        /**
         * Set additional validation for date field
         * @param {object} dateField
         */
        setAditionalValidation: function (dateField) {
            var date = new Date(),
                currentUtcDate = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0)).valueOf() / 1000,
                newValidation = {
                    current_date_or_zero: currentUtcDate
                };
            $.extend(dateField.validation, newValidation);
        },

        /**
         * Reset field to initial value
         * @param {object} dateField
         */
        resetField: function (dateField) {
            var field = $('[name="' + dateField.inputName + '"]'),
                calendarValue = field.val();
            dateField.reset();
            if (calendarValue !== dateField.initialValue) {
                field.val(dateField.initialValue);
            }
        },

        /**
         * Enable date field
         * @param {object} field
         */
        enableDateField: function (field) {
            this.resetField(field);
            field.enable();
        },

        /**
         * Disable date field
         * @param {object} field
         */
        disableDateField: function (field) {
            this.resetField(field);
            field.disable();
        }
    });
});
