/* eslint no-unused-vars: [1] */
var config = {
    config: {
        mixins: {
            'Digidirect_PreOrder/js/product/preorder': {
                'Digidirect_PreOrder/js/product/preorder-mixin': true
            },
            'Digidirect_PreOrder/js/product/preorder_grouped': {
                'Digidirect_PreOrder/js/product/preorder_grouped-mixin': true
            },
            'Digidirect_PreOrder/js/product/preorder_configurable': {
                'Digidirect_PreOrder/js/product/preorder_configurable-mixin': true
            }
        }
    },
    shim: {
        'Digidirect_PreOrder/js/product/preorder': {
            deps: ['outstockNotification']
        }
    }
};
