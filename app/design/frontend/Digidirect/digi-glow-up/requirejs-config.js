var config = {
    deps: [
        'js/custom' // your existing custom file
    ],
    config: {
        mixins: {
            'Magento_Checkout/js/sidebar': {
                'Magento_Theme/js/sidebar-mixin': true
            }
        }
    }
};
