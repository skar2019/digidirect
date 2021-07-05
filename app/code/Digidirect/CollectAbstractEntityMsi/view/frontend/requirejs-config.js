/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'collectPlacesAvailability': 'Digidirect_CollectAbstractEntityMSI/js/availability',
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Digidirect_CollectAbstractEntityMSI/js/extends/swatch-renderer': true
            }
        }
    }
};
