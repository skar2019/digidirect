define(['module', 'exports', 'ewaveStoreCatalog', './../../common/store'], function (module, exports, _ewaveStoreCatalog, _store) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }

    var AddOn = function AddOn() {
        _classCallCheck(this, AddOn);

        _store.Store.on(_store.Events.DATA_FETCH_SUCCESS, function (data) {
            _ewaveStoreCatalog.GlobalStore.emit(_ewaveStoreCatalog.GlobalEvents.PRODUCT_COLLECTION_UPDATED, data);
        });

        _store.Store.on(_store.Events.DATA_FETCH_START, function (data, options) {
            _ewaveStoreCatalog.GlobalStore.emit(_ewaveStoreCatalog.GlobalEvents.PRODUCT_COLLECTION_UPDATE_START, data, options);
        });
    };

    exports.default = AddOn;
    module.exports = exports['default'];
});
