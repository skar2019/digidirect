define([
    'jquery',
    'underscore',
    'jquery/ui'
], function ($, _) {
    'use strict';

    $.widget('ewave.outstockConfigurable', {
        options: {
            spConfig: {},
            availableProducts: [],
            isSalable: 1,
            superSelector: '.super-attribute-select',
            outofstockHideContainer: 'outofstock-button',
            outofstockInput: 'outofstock-input',
            actionContainer: '.product-options-bottom',
            attributeContainer: '.swatch-attribute',
            swatchElements: 'swatch-options',
            swatchEventElements: '.swatch-option',
            hiddenClass: 'no-display',
            visibleClass: '-visible-visible',
            notAvailableClass: '-not-available',
            selectedClass: '.selected'
        },
        productList: [],
        parentID: '',
        swatchMode: false,

        _create: function () {
            var $widget = this,
                input = $widget.element.find($widget.options.superSelector);
            $widget._setProductList();
            $widget.element.on('change', input, function () {
                $widget._checkSimpleProduct();
            });
            if ($('[data-role="' + this.options.swatchElements + '"]').length) {
                $('[data-role="' + this.options.swatchElements + '"]').on('click', $widget.options.swatchEventElements, function () {
                    // Magento error with swatches. setTimeout allows not to block the function call _checkSimpleProduct
                    setTimeout(function () {
                        $widget._checkSimpleProduct();
                        $widget._clearDisabledAttributes();
                        $widget._checkSelectedAttributes();
                    }, 0);
                });
                $widget.swatchMode = true;
                $widget.paremtrsCount = true;
            }
            $widget._getParentProduct();
            if (this.options.isSalable) {
                $widget._checkSimpleProduct();
            } else {
                $widget._showHiddenBlock();
            }
        },
        
        // Remember the id of the parent product, it is used when resetting all parameters
        _getParentProduct: function () {
            this.parentID = $('[data-role="' + this.options.outofstockInput + '"]').val();
        },
        
        // Creates an array with all products
        _setProductList: function () {
            var $widget = this;
            
            for (var key in this.options.spConfig.index) {
                var prod = this.options.spConfig.index[key];
                prod.id = key;
                this.productList.push(prod);
            }

            this.availableList = _.filter(this.productList, function (prod) {
                return $widget.options.availableProducts.indexOf(prod.id) !== -1;
            });
        },
        
        // Checks if the selected product is in the list of available products
        _checkSimpleProduct: function () {
            var $widget = this,
                listProducts = this._getAvailableProducts(),
                avaliableList,
                simpleProduct = listProducts ? _.first(listProducts).id : undefined;

            avaliableList = _.filter(listProducts, function (prod) {
                return $widget.options.availableProducts.indexOf(prod.id) !== -1;
            });
            if (simpleProduct && $widget.options.availableProducts.indexOf(simpleProduct) === -1 && !avaliableList.length) {
                $('[data-role="' + $widget.options.outofstockHideContainer + '"]').addClass(this.options.visibleClass);
                $($widget.options.actionContainer).addClass(this.options.hiddenClass);
            } else {
                $('[data-role="' + $widget.options.outofstockHideContainer + '"]').removeClass(this.options.visibleClass);
                $($widget.options.actionContainer).removeClass(this.options.hiddenClass);
            }
            // Change of the id selected product in the form of notification
            $('[data-role="' + $widget.options.outofstockInput + '"]').val(simpleProduct || $widget.parentID);
        },

        // Returns an array of products that match the selected parameters
        _getAvailableProducts: function () {
            var $widget = this,
                data = {},
                listProducts;

            $widget.element.find($widget.options.superSelector).each(function () {
                var attrName = $(this).attr('name'),
                    attrId = attrName.substring(attrName.indexOf('[') + 1, attrName.length - 1),
                    value = +$(this).val();
                if (value) {
                    data[attrId] = String(value);
                }
            });

            listProducts = _.where(this.productList, data);
            return _.isEmpty(data) ? undefined : listProducts;
        },

        _showHiddenBlock: function () {
            $('[data-role="' + this.options.outofstockHideContainer + '"]').addClass(this.options.visibleClass);
        },

        /**
         * Clear swatch attributes from disabled class
         * @private
         */
        _clearDisabledAttributes: function () {
            $(this.options.swatchEventElements).removeClass(this.options.notAvailableClass);
        },

        /**
         * Check selected attributes
         * @private
         */
        _checkSelectedAttributes: function () {
            var $widget = this;
            $(this.options.swatchEventElements + this.options.selectedClass).each(function () {
                var container = $(this).closest($widget.options.attributeContainer),
                    attributeContainers = $($widget.options.attributeContainer).not(container),
                    attributeID = container.attr('attribute-id'),
                    optionID = $(this).attr('option-id');
                $widget._setDisabledAttributes(attributeContainers, attributeID, optionID);
            });
        },

        /**
         *  Add disabled class to attributes
         * @param {array} containers
         * @private
         */
        _setDisabledAttributes: function (containers, attributeID, optionID) {
            var $widget = this;
            containers.each(function () {
                var attribute = $(this).attr('attribute-id');
                $(this).find($widget.options.swatchEventElements).each(function () {
                    var option = $(this).attr('option-id');
                    if ($widget._checkOption(attributeID, optionID, attribute, option)) {
                        $(this).addClass($widget.options.notAvailableClass);
                    }
                }
                );
            });
        },

        /**
         * Check option in options for available products
         * @param {number} attributeID
         * @param {number} optionID
         * @param {number} attibute
         * @param {number} option
         * @return {*}
         * @private
         */
        _checkOption: function (attributeID, optionID, attibute, option) {
            var obj = {},
                list;

            obj[attributeID] = optionID;
            obj[attibute] = option;
            list = _.where(this.availableList, obj);

            return _.isEmpty(list);
        }

    });

    return $.ewave.outstockConfigurable;
});
