define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({

        /**
         * Initialize component.
         * @returns {Element}
         */
        initialize: function () {
            return this
                ._super()
                .changeVisible(this.initialValue);
        },

        /**
         * Callback that fires when 'value' property is updated.
         *
         * @param {String} currentValue
         * @returns {*}
         */
        onUpdate: function (currentValue) {
            this.changeVisible(currentValue);

            return this._super();
        },

        changeVisible: function (currentValue) {
            var
                resultLoadActionField = uiRegistry.get('index = result_load_action'),
                resultLoadUrlField = uiRegistry.get('index = result_load_url'),
                productsCountField = uiRegistry.get('index = products_count'),
                productsCountCategoryField = uiRegistry.get('index = products_count_for_category');

            if (resultLoadActionField) {
                resultLoadActionField.visible(parseInt(currentValue));
            }
            if (resultLoadUrlField) {
                resultLoadUrlField.visible(parseInt(currentValue));
            }
            if (productsCountField) {
                productsCountField.visible(!parseInt(currentValue));
            }
            if (productsCountCategoryField) {
                productsCountCategoryField.visible(!parseInt(currentValue));
            }
        }
    });
});