/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'collectPlacesAvailability': 'Ewave_CollectAbstractEntityMSI/js/availability',
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Ewave_CollectAbstractEntityMSI/js/extends/swatch-renderer': true
            }
        }
    }
};
