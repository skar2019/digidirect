/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Ewave_Collect/js/view/block': {
                'Ewave_CollectAbstractEntity/js/view/block-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ewave_CollectAbstractEntity/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'Ewave_CollectAbstractEntity/js/model/shipping-rates-validator-mixin': true
            }
        }
    }
};
