/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'addToCompare': 'Magento_Catalog/js/add-to-compare'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/price-box': {
                'Magento_Catalog/js/extends/price-box': true
            },
            'mage/dataPost': {
                'Magento_Catalog/js/extends/dataPost': true
            }
        }
    }
};
