define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            productType: 'product',
            productChooserButtonSelector: '.js-product-chooser-button',
            targetTypeSelector: '[name="target_type"]'
        },

        initObservable: function () {
            var self = this;
            $.async(this.productChooserButtonSelector, function () {
                self.switchButtonVisibility($(self.targetTypeSelector).val());
            });

            return this._super();
        },

        onUpdate: function () {
            this._super();
            this.switchButtonVisibility(this.value());
        },

        switchButtonVisibility(value)
        {
            if (value === this.productType) {
                $(this.productChooserButtonSelector).show();
            } else {
                $(this.productChooserButtonSelector).hide();
            }
        }
    });
});
