define([
    'jquery',
    'underscore',
    'jquery/ui'
], function ($, _) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            options: {
                applicableSimple: {},
                productConteiner: '.product-item-info',
                labelContainer: '[data-role="label-overlay"]'
            },
            labelsForParent: '',
            _create: function () {
                this._super();
                this._findProductOverlay();
                var labels = this.options.parentLabels;
                if (labels) {
                    for (var key in labels) {
                        if (labels.hasOwnProperty(key)) {
                            this.labelsForParent += labels[key];
                        }
                    }
                }

                this._bindOverlay();
            },

            _bindOverlay: function () {
                if (!this.inProductList) {
                    $(this.element).on('product.overlay.appended', $.proxy(function () {
                        this._findProductOverlay();
                    }, this));
                }
            },

            _findProductOverlay: function () {
                var simpleId = this.getProduct(),
                    counter = this.getProductCounter(),
                    productContainer = this.options.productConteiner,
                    overlayProduct = $(this.element).closest(productContainer).find('.product-overlay'),
                    labelContainer = this.options.labelContainer;

                if (this.options.isLoaded) {
                    $(labelContainer).empty();
                } else {
                    this.options.isLoaded = true;
                }

                overlayProduct.addClass('-hide');
                if (typeof this.options.applicableSimple[simpleId] !== 'undefined' && counter === 1) {
                    $.each(this.options.applicableSimple[simpleId], function (i, overlay) {
                        var overlayId = 'product-overlay-' + overlay.overlay_id + '-' + simpleId,
                            stockLabel = overlay.stock_label,
                            labelResult = stockLabel + $(labelContainer).html();
                        $('.' + overlayId).removeClass('-hide');
                        $(labelContainer).html(labelResult);
                    });
                } else if (typeof simpleId === 'undefined') {
                    if (this.labelsForParent !== '') {
                        $(labelContainer).html(this.labelsForParent);
                    }
                    $(this.element).closest(productContainer).find('[data-role="use-for-parent"]').removeClass('-hide');
                } else {
                    overlayProduct.addClass('-hide');
                }
            },

            getProductCounter: function () {
                var products = this._CalcProducts();

                return products.length;
            },

            updateBaseImage: function (images, context, isInProductView) {
                this._super(images, context, isInProductView);

                if (this.inProductList) {
                    this._findProductOverlay();
                }
            }
        });
        return $.mage.SwatchRenderer;
    };
});
