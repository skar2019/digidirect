/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'mainMenu': 'Ewave_Navigation/js/main-menu'
        }
    },
    deps: [
        'babelpolyfill'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Ewave_Navigation/js/product/breadcrumbs-mixin': true
            }
        }
    }
};
