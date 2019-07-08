/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'navigation': 'Ewave_Navigation/js/navigation',
            'mainMenu': 'Ewave_Navigation/js/main-menu'
        }
    },
    deps: [
        'babelpolyfill',
        'jquery',
        'ewaveUtils',
        'Ewave_Navigation/js/dist/common/component',
        'Ewave_Navigation/js/dist/common/store',
        'Ewave_Navigation/js/dist/views/index',
        'Ewave_Navigation/js/dist/common/constants',
        'Ewave_Utilities/js/dist/vendor/event-pubsub',
        'matchMedia',
        'Ewave_Navigation/js/dist/views/actions/static/action',
        'Ewave_Navigation/js/dist/views/actions/common/offcanvas',
        'Ewave_Navigation/js/dist/views/actions/dynamic/bindings',
        'wcagHandler',
        'Ewave_Navigation/js/navigation'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Ewave_Navigation/js/product/breadcrumbs-mixin': true
            }
        }
    }
};
