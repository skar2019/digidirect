define(['module', 'exports', 'jquery', 'matchMedia', './../../../common/store'], function (module, exports, _jquery, _matchMedia, _store) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _jquery2 = _interopRequireDefault(_jquery);

    var _matchMedia2 = _interopRequireDefault(_matchMedia);

    function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
            default: obj
        };
    }

    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }

    var _createClass = function () {
        function defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }

        return function (Constructor, protoProps, staticProps) {
            if (protoProps) defineProperties(Constructor.prototype, protoProps);
            if (staticProps) defineProperties(Constructor, staticProps);
            return Constructor;
        };
    }();

    var Offcanvas = function () {
        /**
         * initialize offcanvas logic
         * @param options
         * @param vm
         */
        function Offcanvas(options, vm) {
            _classCallCheck(this, Offcanvas);

            this.options = Object.assign({}, this.options, options);
            this.watchers();
            this.bind(vm);
        }

        _createClass(Offcanvas, [{
            key: 'watchers',
            value: function watchers() {
                var _this = this;

                _store.Store.on(_store.Events.OFFCANVAS_OFF, function (data) {
                    return _this.offCanvasDisable(data);
                });
                _store.Store.on(_store.Events.OFFCANVAS_ON, function (data) {
                    return _this.offCanvasEnable(data);
                });
            }
        }, {
            key: 'offCanvasEnable',
            value: function offCanvasEnable(view) {
                if (view.options.action !== 'click') {
                    view._toggleAction('click');
                }
                var navigation = (0, _jquery2.default)(this.options.area + ' ' + this.options.wrapperClass);
                if (navigation.hasClass('-horizontal')) {
                    navigation.removeClass('-horizontal');
                }
                if (!navigation.hasClass('-expanded')) {
                    navigation.addClass('-expanded');
                }
                view._collapseSub();
            }
        }, {
            key: 'offCanvasDisable',
            value: function offCanvasDisable(view) {
                if (view.options.action !== this.options.action) {
                    view._toggleAction(this.options.action);
                }
                var navigation = (0, _jquery2.default)(this.options.area + ' ' + this.options.wrapperClass),
                    htmlContainer = (0, _jquery2.default)('html');
                if (this.options.horizontal) {
                    navigation.addClass('-horizontal');
                }
                if (this.options.expanded) {
                    if (!navigation.hasClass('-expanded')) {
                        navigation.addClass('-expanded');
                    }
                } else {
                    navigation.removeClass('-expanded');
                }
                view._collapseSub();
                if (htmlContainer.hasClass(this.options.offCanvasClass)) {
                    htmlContainer.removeClass(this.options.offCanvasClass);
                }
            }
        }, {
            key: 'bind',
            value: function bind(vm) {
                var _this2 = this;

                var self = this;
                (0, _jquery2.default)(this.options.togglerSelector).on(this.options.offCanvasEvent, function () {
                    _this2.toggle();
                });
                (0, _matchMedia2.default)({
                    media: '(max-width: ' + self.options.breakpoint + ' )',
                    entry: _jquery2.default.proxy(function () {
                        _store.Store.emit(_store.Events.OFFCANVAS_ON, vm);
                    }, this),
                    exit: _jquery2.default.proxy(function () {
                        _store.Store.emit(_store.Events.OFFCANVAS_OFF, vm);
                    }, this)
                });
            }
        }, {
            key: 'toggle',
            value: function toggle() {
                var _this3 = this;

                var htmlContainer = (0, _jquery2.default)('html');
                if (htmlContainer.hasClass(this.options.offCanvasClass)) {
                    htmlContainer.removeClass(this.options.offCanvasClass);
                } else {
                    setTimeout(function () {
                        htmlContainer.addClass(_this3.options.offCanvasClass);
                    }, 42);
                }
            }
        }]);

        return Offcanvas;
    }();

    exports.default = Offcanvas;
    module.exports = exports['default'];
});
