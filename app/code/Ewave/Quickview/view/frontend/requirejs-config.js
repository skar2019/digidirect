/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'quickView': 'Ewave_QuickView/js/quickview',
            'quickViewAddToCart': 'Ewave_QuickView/js/quickview-add-to-cart',
            'quickViewPageEvents': 'Ewave_QuickView/js/quickview-page-events'
        }
    },
    config: {
        mixins: {
            'Magento_MultipleWishlist/js/multiple-wishlist': {
                'Ewave_QuickView/js/multiple-wishlist-extend': true
            },
            'mage/gallery/gallery': {
                'Ewave_QuickView/js/gallery-extend': true
            }
        }
    },
    deps: [
        'babelpolyfill'
    ]
};
