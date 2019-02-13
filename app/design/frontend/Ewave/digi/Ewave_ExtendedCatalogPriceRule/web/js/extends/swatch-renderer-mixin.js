define([
    'jquery',
    'underscore',
    'mage/translate',
    'mage/template',
    'text!Ewave_ExtendedCatalogPriceRule/template/display_message.html',
    'text!Ewave_ExtendedCatalogPriceRule/template/display_message_plp.html',
    'priceUtils',
    'catalogPriceRuleModal',
    'Ewave_ExtendedCatalogPriceRule/js/extends/swatch-renderer',
    'jquery/ui'
], function ($, _, $t, mageTemplate, messageTemplate, plpMessageTemplate, priceUtils, catalogPriceRuleModal) {
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
                    _.each(result, function (rule) {
                        if (typeof rule.action_amount !== 'string') {
                            var currentOptionPrice = this.getCurrentOptionPrice($widget.options.jsonConfig, rule.product_id);

                            rule.before_cashback_label = this.options.extendedRulesTranslation.beforeCashback;
                            rule.after_cashback_label = this.options.extendedRulesTranslation.afterCashback;

                            if (currentOptionPrice) {
                                rule.before_cashback_price = priceUtils.formatPrice(+currentOptionPrice.finalPrice.amount, $widget.options.jsonConfig.priceFormat);
                                rule.after_cashback_price = priceUtils.formatPrice((+currentOptionPrice.finalPrice.amount - rule.action_amount), $widget.options.jsonConfig.priceFormat);
                            } else {
                                console.error('Can\'t find current option price');
                                rule.before_cashback_price = 0;
                                rule.after_cashback_price = 0;
                            }
                        }
                        rule.pdp_description = this.decodeEscapedHtml(rule.pdp_description);

                        $(mageTemplate(plpMessageTemplate, {
                            data: rule,
                            itemIndex: selectedIndex,
                            productId: $widget.options.jsonConfig.productId, // Parent ID
                            label: $widget.options.extendedRulesTranslation,
                            inProductList: $widget.inProductList
                        })).insertBefore($widget.element.parents(this.options.selectorProductTile).find(this.options.selectorProductPrice));
                    }, this);
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
                    $widget.element.parents(this.options.selectorProductTile).find(this.options.selectorProductPrice).prevAll(this.options.extendedRulesBlock).remove();
                } else {
                    $('.product-info-main .product-info-price .price-box .price-container').prevAll(this.options.extendedRulesBlock).remove();
                }
            },
            getCurrentOptionPrice: function (jsonConfig, productId) {
                if (!jsonConfig || !jsonConfig.optionPrices || !productId) {
                    return false;
                }
                return jsonConfig.optionPrices[productId];
            }
        });

        return $.mage.SwatchRenderer;
    };
});
