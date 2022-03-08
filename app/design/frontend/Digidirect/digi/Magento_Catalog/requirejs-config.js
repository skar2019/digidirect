var config = {
    config: {
        mixins: {
            'Magento_Catalog/js/product/list/toolbar': {
                'Magento_Catalog/js/product/list/toolbar-mixin': true
            }
        }
    },
    map: {
        '*': {
            'cashbackLabel': 'Magento_Catalog/js/product/price/cashback-label',
            'reposition': 'Magento_Catalog/js/product/price/reposition',
            'categoryPageRender': 'Magento_Catalog/js/listing/category-page'
        }
    }
};
