define([
    'jquery',
    'jquery/ui',
    'preOrder'
], function ($) {
    'use strict';

    $.widget('ewave.preOrderConfigurable', $.ewave.preOrder, {
        options: {
            isAllProductsPreorder: 0,
            map: [],
            currentAttributes: {}
        },

        _create: function () {
            this._super();
            this.bind();
        },

        bind: function () {
            var self = this;
            if (this.options.isAllProductsPreorder === 1) {
                this.enable();
            }

            $('.swatch-opt').on('click change', function () {
                self.update();
            });
        },

        update: function () {
            var attributeValue,
                isChanged = false,
                $element;
            for (var attributeId in this.options.currentAttributes) {
                attributeValue = this.options.currentAttributes[attributeId];
                $element = $('[attribute-id=' + attributeId + ']');
                if (!$element.length) {
                    console.log('error');
                    return;
                }
                if ($element.attr('option-selected') != attributeValue) {
                    isChanged = true;
                    this.options.currentAttributes[attributeId] = $element.attr('option-selected');
                }
            }
            if (isChanged) {
                this.onChangeProductAttributes();
            }
        },

        onChangeProductAttributes: function () {
            var currentProductId = false,
                attributeValue,
                productInfo;
            for (var productId in this.options.map) {
                productInfo = this.options.map[productId];
                currentProductId = productId;
                for (var attributeId in this.options.currentAttributes) {
                    attributeValue = this.options.currentAttributes[attributeId];
                    if (productInfo.attributes[attributeId] != attributeValue) {
                        currentProductId = false;
                        break;
                    }
                }
                if (currentProductId) {
                    break;
                }
            }

            if (this.options.map[currentProductId]) {
                this.options.addToCartLabel = this.options.map[currentProductId]['cartLabel'];
                this.options.preOrderNote = this.options.map[currentProductId]['note'];
                this.enable();
            } else {
                this.disable();
            }
        }
    });

    return $.ewave.preOrderConfigurable;
});
