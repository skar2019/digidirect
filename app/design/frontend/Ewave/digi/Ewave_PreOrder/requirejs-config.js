/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Ewave_PreOrder/js/product/preorder': {
                'Ewave_PreOrder/js/product/preorder-mixin': true
            },
            'Ewave_PreOrder/js/product/preorder_grouped': {
                'Ewave_PreOrder/js/product/preorder_grouped-mixin': true
            },
            'Ewave_PreOrder/js/product/preorder_configurable': {
                'Ewave_PreOrder/js/product/preorder_configurable-mixin': true
            }
        }
    },
    shim: {
        'Ewave_PreOrder/js/product/preorder': {
            deps: ['outstockNotification']
        }
    }
};
