/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Ewave_Collect/js/view/block': {
                'Ewave_CollectAbstractEntity/js/view/block-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ewave_CollectAbstractEntity/js/view/shipping-mixin': true
            }
        }
    }
};
