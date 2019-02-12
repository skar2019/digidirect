define([
    'jquery',
    'underscore',
    'mage/translate',
    'mage/template',
    'text!Ewave_ExtendedCatalogPriceRule/template/display_message.html',
    'priceUtils',
    'catalogPriceRuleModal',
    'Ewave_ExtendedCatalogPriceRule/js/extends/swatch-renderer',
    'jquery/ui'
], function ($, _, $t, mageTemplate, messageTemplate, priceUtils, catalogPriceRuleModal) {
    'use strict';

    return function (target) {
        $.widget('mage.SwatchRenderer', target, {
            options: {
                extendedRulesTranslation: {
                    defaultRuleLabel: $t('Special Offer'),
                    afterCashback: $t('After Cashback'),
                    beforeCashback: $t('Before Cashback')
                }
            },
            renderExtendedRules: function ($widget, result, selectedIndex) {
                if ($widget.inProductList) {
                    this._super($widget, result, selectedIndex);

                } else {
                    _.each(result, function (rule) {
                        if (typeof rule.action_amount !== 'string') {
                            rule.action_amount = priceUtils.formatPrice(rule.action_amount, $widget.options.jsonConfig.priceFormat);
                        }
                        rule.pdp_description = this.decodeEscapedHtml(rule.pdp_description);

                        $(mageTemplate(messageTemplate, {
                            data: rule,
                            itemIndex: selectedIndex,
                            productId: $widget.options.jsonConfig.productId, // Parent ID
                            label: $widget.options.extendedRulesTranslation,
                            inProductList: $widget.inProductList
                        })).insertBefore($('.product-info-main .product-info-price .price-box .price-container:first'));

                        $('.product-info-main [data-role="priceBox"] .price-final_price .price-label').text(this.options.extendedRulesTranslation.beforeCashback);
                    }, this);
                }
            },
            cleanExtendedRules: function ($widget) {
                if ($widget.inProductList) {
                    this._super($widget);
                } else {
                    $('.product-info-main .product-info-price .price-box .price-container').prevAll(this.options.extendedRulesBlock).remove();
                }
            },
        });

        return $.mage.SwatchRenderer;
    };
});
