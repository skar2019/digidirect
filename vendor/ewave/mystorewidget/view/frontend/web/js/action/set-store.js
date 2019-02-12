define([
    'Ewave_MyStoreWidget/js/model/store'
], function (store) {
    'use strict';

    return function (item) {
        store.entityId(item.entityId);
        store.selectedStore(item.selectedStore);
        store.storeUrl(item.storeUrl);
        store.isStoreSelected(item.isStoreSelected)
    };
});
