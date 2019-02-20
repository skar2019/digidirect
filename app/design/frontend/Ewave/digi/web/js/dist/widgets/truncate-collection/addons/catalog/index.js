define(['module', 'exports', 'Ewave_InfiniteScroll/js/dist/common/store'], function (module, exports, _store2) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    function _objectWithoutProperties(obj, keys) {
        var target = {};

        for (var i in obj) {
            if (keys.indexOf(i) >= 0) continue;
            if (!Object.prototype.hasOwnProperty.call(obj, i)) continue;
            target[i] = obj[i];
        }

        return target;
    }

    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }

    var AddOn = function AddOn(_ref) {
        var _this = this;

        var options = _ref.options,
            _store = _objectWithoutProperties(_ref, ['options']);

        _classCallCheck(this, AddOn);

        this.Store = _store.store;
        this.Events = _store.events;

        _store2.Store.on(_store2.Events.DATA_FETCH_SUCCESS, function () {
            _this.Store.emit(_this.Events.TRUNCATE_RAW_COLLECTION);
        });
        _store2.Store.on(_store2.Events.DATA_FETCH_FINISH, function () {
            _this.Store.emit(_this.Events.TRUNCATE_RAW_COLLECTION);
        });
        _store2.Store.on(_store2.Events.RELOAD, function () {
            _this.Store.emit(_this.Events.TRUNCATE_RAW_COLLECTION);
        });
    };

    exports.default = AddOn;
    module.exports = exports['default'];
});
