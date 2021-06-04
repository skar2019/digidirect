var config = {
    map: {
        '*': {
            modalMystorewidget: 'Ewave_MyStoreWidget/js/modal-MyStoreWidget'
        }
    },
    config: {
        mixins: {
            'Ewave_MyStoreWidget/js/my-store-switcher': {
                'Ewave_MyStoreWidget/js/my-store-switcher-mixin': true
            },
            'Ewave_MyStoreWidget/js/view/mystore-widget': {
                'Ewave_MyStoreWidget/js/view/mystore-widget-mixin': true
            }
        }
    }
};
