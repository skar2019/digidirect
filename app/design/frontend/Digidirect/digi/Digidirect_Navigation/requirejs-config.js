/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'navigation': 'Digidirect_Navigation/js/navigation',
            'mainMenu': 'Digidirect_Navigation/js/main-menu'
        }
    },
    deps: [
        'babelpolyfill',
        'jquery',
        'digidirectUtils',
        'Digidirect_Navigation/js/dist/common/component',
        'Digidirect_Navigation/js/dist/common/store',
        'Digidirect_Navigation/js/dist/views/index',
        'Digidirect_Navigation/js/dist/common/constants',
        'Digidirect_Utilities/js/dist/vendor/event-pubsub',
        'matchMedia',
        'Digidirect_Navigation/js/dist/views/actions/static/action',
        'Digidirect_Navigation/js/dist/views/actions/common/offcanvas',
        'Digidirect_Navigation/js/dist/views/actions/dynamic/bindings',
        'wcagHandler',
        'Digidirect_Navigation/js/navigation'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Digidirect_Navigation/js/product/breadcrumbs-mixin': true
            }
        }
    }
};
