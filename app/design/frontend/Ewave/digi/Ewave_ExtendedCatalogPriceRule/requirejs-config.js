/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'catalogPriceRuleModal': 'Ewave_ExtendedCatalogPriceRule/js/rule-modal'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Ewave_ExtendedCatalogPriceRule/js/extends/swatch-renderer-mixin': true
            }
        }
    }
};
