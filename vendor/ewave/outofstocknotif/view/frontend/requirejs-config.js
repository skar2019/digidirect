/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'outstockNotification': 'Ewave_OutOfStockNotif/js/outstock-notification',
            'outstockConfigurable': 'Ewave_OutOfStockNotif/js/outstock-configurable'
        }
    },
    config: {
        mixins: {
            'Magento_Catalog/js/catalog-add-to-cart': {
                'Ewave_OutOfStockNotif/js/catalog-add-to-cart-extend': true
            }
        }
    }
};
