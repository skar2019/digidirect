define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('ewave.bannerDependency', {
        options: {
            selectedProduct: "input[name*='product_id']",
            typeSelect: "select[name*='target_type']",
            product: 'product',
            selectNavigationType: "select[name*='navigation_title_type']",
            wysiwygTextarea: "textarea[name*='navigation_title']",
            wysiwyg: 1
        },

        _create: function () {
            this._events();
        },

        _events: function () {
            var self = this,
                $optionsSelect = $(this.options.typeSelect),
                $navigationTypeSelect = $(this.options.selectNavigationType);

            this._switchProductField($optionsSelect.val());
            this.switchWysiwyg($navigationTypeSelect.val());

            $navigationTypeSelect.on('change', function () {
                self.switchWysiwyg($(this).val());
            });

            $optionsSelect.on('change', function () {
                self._switchProductField($(this).val());
            });
        },

        switchWysiwyg: function (value) {
            var $wysiwygField = $(this.options.wysiwygTextarea).closest('.admin__field.field');
            if (value != this.options.wysiwyg) {
                $wysiwygField.hide();
            } else {
                $wysiwygField.show();
            }
        },

        _switchProductField: function (value) {
            var selectedProduct = $(this.options.selectedProduct).closest('.admin__field.field');
            if (value != this.options.product) {
                selectedProduct.hide().prev().hide();
                $(this.options.selectedProduct).removeClass('required-entry');
            } else {
                selectedProduct.show().prev().show();
                $(this.options.selectedProduct).addClass('required-entry');
            }
        }
    });

    return $.ewave.bannerDependency;
});
