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

    var AddOn = function AddOn(options) {
        var _this = this;

        _classCallCheck(this, AddOn);

        _ewaveStoreCatalog.GlobalStore.on(_ewaveStoreCatalog.GlobalEvents.PRODUCT_COLLECTION_UPDATED, function (data) {
            if (data.page_params.nextUrl) {
                data.page_params.nextUrl = data.page_params.nextUrl + '&_is=' + options.perPageCount;
            }
            _this.options = Object.assign({}, options, data.page_params);
            _store.Store.emit(_store.Events.RELOAD, _this.options);
        });
    };

    exports.default = AddOn;
    module.exports = exports['default'];
});
