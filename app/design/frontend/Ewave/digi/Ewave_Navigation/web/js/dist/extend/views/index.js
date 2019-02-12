define(['module', 'exports', 'jquery', '../../views/index', 'mage/template', 'text!Ewave_Navigation/template/back.html'], function (module, exports, _jquery, _index, _template, _back) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _jquery2 = _interopRequireDefault(_jquery);

    var _index2 = _interopRequireDefault(_index);

    var _template2 = _interopRequireDefault(_template);

    var _back2 = _interopRequireDefault(_back);

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

    function _possibleConstructorReturn(self, call) {
        if (!self) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return call && (typeof call === "object" || typeof call === "function") ? call : self;
    }

    var _get = function get(object, property, receiver) {
        if (object === null) object = Function.prototype;
        var desc = Object.getOwnPropertyDescriptor(object, property);

        if (desc === undefined) {
            var parent = Object.getPrototypeOf(object);

            if (parent === null) {
                return undefined;
            } else {
                return get(parent, property, receiver);
            }
        } else if ("value" in desc) {
            return desc.value;
        } else {
            var getter = desc.get;

            if (getter === undefined) {
                return undefined;
            }

            return getter.call(receiver);
        }
    };

    function _inherits(subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
                value: subClass,
                enumerable: false,
                writable: true,
                configurable: true
            }
        });
        if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
    }

    var ViewCustom = function (_View) {
        _inherits(ViewCustom, _View);

        function ViewCustom() {
            _classCallCheck(this, ViewCustom);

            return _possibleConstructorReturn(this, (ViewCustom.__proto__ || Object.getPrototypeOf(ViewCustom)).apply(this, arguments));
        }

        _createClass(ViewCustom, [{
            key: 'loadExtraLogic',
            value: function loadExtraLogic() {
                var _this2 = this;

                _get(ViewCustom.prototype.__proto__ || Object.getPrototypeOf(ViewCustom.prototype), 'loadExtraLogic', this).call(this);

                if (this.options.responsive) {
                    var links = (0, _jquery2.default)(this.options.wrapperClass + ' > .-parent > ' + this.options.itemLabelClass),
                        name = void 0,
                        tmpl = void 0;

                    links.each(function (index, element) {
                        name = (0, _jquery2.default)(element).text();

                        tmpl = (0, _template2.default)(_back2.default, {
                            name: name
                        });

                        (0, _jquery2.default)(tmpl).prependTo((0, _jquery2.default)(element).parent(_this2.options.itemClass).find(_this2.options.subMenuBlockClass).first());
                    });

                    links.on('click', function () {
                        (0, _jquery2.default)('.menu-section').addClass('-sub-slide');
                    });

                    (0, _jquery2.default)(this.options.area + ' ' + this.options.subMenuBlockClass + ' > .menu-back').on('click', function (e) {
                        var $this = (0, _jquery2.default)(e.currentTarget);

                        $this.closest(_this2.options.subMenuBlockClass).removeClass('-open');
                        $this.closest(_this2.options.itemClass).removeClass('-open');

                        (0, _jquery2.default)('.menu-section').removeClass('-sub-slide');
                    });
                }
            }
        }]);

        return ViewCustom;
    }(_index2.default);

    exports.default = ViewCustom;
    module.exports = exports['default'];
});
