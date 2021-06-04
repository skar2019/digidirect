/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'productOverlay': 'Ewave_ProductOverlay/js/overlay'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Ewave_ProductOverlay/js/swatch-overlay': true
            }
        }
    }
};
