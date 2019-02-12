define(['module', 'exports', 'Magento_Ui/js/lib/view/utils/async'], function (module, exports, _async) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _async2 = _interopRequireDefault(_async);

    function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
            default: obj
        };
    }

    function _defineProperty(obj, key, value) {
        if (key in obj) {
            Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
            });
        } else {
            obj[key] = value;
        }

        return obj;
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

    var FormLabel = function () {
        function FormLabel() {
            var _this = this;

            var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

            _classCallCheck(this, FormLabel);

            this.options = Object.assign({}, {
                selector: '.field:not(.choice):not(.-custom)',
                formElements: 'input, select, textarea',
                activeClass: '-active'
            }, options);
            this.pool = _defineProperty({}, this.options.selector, new Map());

            _async2.default.async(this.options.formElements, function (node) {
                (0, _async2.default)(node).filter(_this.hasClosest(_this.options)).each(_this.initLabel(_this.options));
            });
        }

        _createClass(FormLabel, [{
            key: 'hasClosest',
            value: function hasClosest(options) {
                var _this2 = this;

                return function (i, el) {
                    return _this2.getClosestElement(el, options.selector);
                };
            }
        }, {
            key: 'getClosestElement',
            value: function getClosestElement(el, selector) {
                var ctxPool = this.pool[selector],
                    closestElement = void 0;

                if (ctxPool.has(el)) {
                    closestElement = ctxPool.get(el);
                } else {
                    closestElement = (0, _async2.default)(el).closest(selector)[0];
                    ctxPool.set(el, closestElement);
                }

                return closestElement;
            }
        }, {
            key: 'initLabel',
            value: function initLabel(options) {
                var _this3 = this;

                return function (i, el) {
                    var ctxPool = _this3.pool[options.selector],
                        fieldElement = ctxPool.get(el);

                    if (el.value.trim() !== '' || _this3.isAutoFill((0, _async2.default)(el))) {
                        fieldElement.classList.add(options.activeClass);
                    }

                    (0, _async2.default)(el).on('focus change', function () {
                        fieldElement.classList.add(options.activeClass);
                    }).on('blur', function (e) {
                        if (e.target.value.trim() === '') {
                            fieldElement.classList.remove(options.activeClass);
                        }
                    });

                    ctxPool.delete(el);
                };
            }
        }, {
            key: 'isAutoFill',
            value: function isAutoFill($el) {
                try {
                    return $el.is(':-webkit-autofill');
                } catch (e) {
                    return false;
                }
            }
        }]);

        return FormLabel;
    }();

    exports.default = FormLabel;


    new FormLabel(module.config());
    module.exports = exports['default'];
});
