define([
    'jquery',
    'jquery/ui',
    'preOrder'
], function ($) {
    'use strict';

    $.widget('ewave.preOrderBundle', $.ewave.preOrder, {
        options: {
            isAllProductsPreorder: 0,
            map: {},
            optionsData: {},
            checkedElements: {}
        },

        _create: function () {
            this._super();
            this.bind();
        },

        bind: function () {
            if (this.options.isAllProductsPreorder === 1) {
                this.enable();
                this.options.availabilityElement.html(this.options.preOrderNote);
            }
            var self = this,
                option,
                $place;
            for (var optionId in this.options.optionsData) {
                option = this.options.optionsData[optionId];
                $place = $($('#bundle-option-' + optionId + '-qty-input').parents('.field.option')[0]);

                if (option.isRequired && option.isPreorder && option.isSingle) {
                    self._enableOrDisablePreorder({
                        mapId: optionId + '-' + option.selectionId,
                        optionId: optionId,
                        selectionId: option.selectionId
                    }, $place, true);
                }
            }

            $('.bundle-options-wrapper .bundle-option-select').on('change', function (event) {
                var element = event.currentTarget,
                    elementInfo = self._getElementInfo(element),
                    $place = $($(element).parents('.field.option')[0]);
                self._enableOrDisablePreorder(elementInfo, $place);
            });

            $('.bundle-options-wrapper .checkbox, .bundle-options-wrapper .radio').on('change', function (event) {
                var element = event.currentTarget,
                    $place = $($(element).parents('.field.option')[0]),
                    elementInfo = self._getElementInfo(element),
                    isSelect = $(element).is(':checked');
                if (element.type === 'radio') {
                    self._enableOrDisablePreorder(elementInfo, $place, isSelect);
                } else {
                    self._enableOrDisablePreorderMultiselection(elementInfo, $place, isSelect);
                }
            });

            $('.bundle-options-wrapper .multiselect').on('change', function (event) {
                var element = event.currentTarget,
                    $element = $(element),
                    $place = $($element.parents('.field.option')[0]),
                    elementInfo = self._getElementInfo(element),
                    isSelect;

                $.each($element.find('option'), function (key, option) {
                    isSelect = false;
                    elementInfo.selectionId = $(option).val();
                    elementInfo.mapId = elementInfo.optionId + '-' + elementInfo.selectionId;
                    $.each($element.val(), function (valueKey, value) {
                        if ($(option).val() == value) {
                            isSelect = true;
                            return;
                        }
                    });
                    self._enableOrDisablePreorderMultiselection(elementInfo, $place, isSelect);
                });
            });
        },

        _changeLabels: function () {
            $.mage.catalogAddToCart.prototype.options.addToCartButtonTextDefault = this.options.addToCartLabel;
            this._setButtonLabel(this.options.addToCartLabel);
        },

        _getElementInfo: function (element) {
            var elementInfo = {
                mapId: 0,
                optionId: 0,
                selectionId: 0
            };
            elementInfo.mapId = element.id.substring(element.id.indexOf('bundle-option-') + String('bundle-option-').length);
            if ($(element).prop('tagName').toLowerCase() === 'select') {
                elementInfo.optionId = elementInfo.mapId;
                elementInfo.selectionId = $(element).val();
                elementInfo.mapId += '-' + elementInfo.selectionId;
            } else {
                if (elementInfo.mapId.indexOf('-') > -1) {
                    elementInfo.optionId = elementInfo.mapId.substring(0, elementInfo.mapId.indexOf('-'));
                    elementInfo.selectionId = elementInfo.mapId.substring(elementInfo.mapId.indexOf('-') + 1);
                } else {
                    elementInfo.optionId = elementInfo.mapId;
                }
            }
            return elementInfo;
        },

        _enableOrDisablePreorder: function (elementInfo, $place, isNeedEnable) {
            var $container = $('#bundle-option-' + elementInfo.optionId + '-preorder-note'),
                counter = 0;
            if (this.options.map[elementInfo.mapId]) {
                if ($container.length === 0) {
                    this._addBundleNote($place, elementInfo.optionId, this.options.map[elementInfo.mapId].note);
                } else {
                    $container.html(this.options.map[elementInfo.mapId].note);
                }
                this.options.checkedElements[elementInfo.optionId] = true;
                if (isNeedEnable) {
                    this.enable();
                }
            } else {
                if ($container.length > 0) {
                    $container.html('');
                }
                this.options.checkedElements[elementInfo.optionId] = false;

                for (var key in this.options.checkedElements) {
                    if (this.options.checkedElements[key]) {
                        counter++;
                    }
                }
                if (counter === 0) {
                    this.disable();
                }
            }
        },

        _enableOrDisablePreorderMultiselection: function (elementInfo, $place, isSelect) {
            var $container = $('#bundle-option-' + elementInfo.mapId + '-preorder-note'),
                counter = 0;
            if (this.options.map[elementInfo.mapId] && isSelect) {
                if ($container.length === 0) {
                    this._addBundleNote($place, elementInfo.mapId, this.options.map[elementInfo.mapId].note);
                } else {
                    $container.html(this.options.map[elementInfo.mapId].note);
                }
                this.options.checkedElements[elementInfo.mapId] = true;
                this.enable();
            } else {
                if ($container.length > 0) {
                    $container.html('');
                }
                this.options.checkedElements[elementInfo.mapId] = false;

                for (var key in this.options.checkedElements) {
                    if (this.options.checkedElements[key]) {
                        counter++;
                    }
                }
                if (counter === 0) {
                    this.disable();
                }
            }
        },

        _addBundleNote: function ($place, id, note) {
            $place.append('<div class="field -preorder"><span class="preorder-note -product" id="bundle-option-' + id + '-preorder-note">' + note + '</span></div>');
        }
    });

    return $.ewave.preOrderBundle;
});
