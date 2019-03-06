/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Ewave_PreOrder/js/product/preorder': {
                'Ewave_PreOrder/js/product/preorder-mixin': true
            },
            'Ewave_PreOrder/js/product/preorder_grouped': {
                'Ewave_PreOrder/js/product/preorder_grouped-mixin': true
            }
        }
    },
    shim: {
        'Ewave_PreOrder/js/product/preorder': {
            deps: ['outstockNotification']
        }
    }
};
