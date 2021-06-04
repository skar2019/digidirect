define(['module', 'exports', 'knockout', 'jquery'], function (module, exports, _knockout, _jquery) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });
    exports.default = bindings;

    var _knockout2 = _interopRequireDefault(_knockout);

    var _jquery2 = _interopRequireDefault(_jquery);

    function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
            default: obj
        };
    }

    var _slicedToArray = function () {
        function sliceIterator(arr, i) {
            var _arr = [];
            var _n = true;
            var _d = false;
            var _e = undefined;

            try {
                for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
                    _arr.push(_s.value);

                    if (i && _arr.length === i) break;
                }
            } catch (err) {
                _d = true;
                _e = err;
            } finally {
                try {
                    if (!_n && _i["return"]) _i["return"]();
                } finally {
                    if (_d) throw _e;
                }
            }

            return _arr;
        }

        return function (arr, i) {
            if (Array.isArray(arr)) {
                return arr;
            } else if (Symbol.iterator in Object(arr)) {
                return sliceIterator(arr, i);
            } else {
                throw new TypeError("Invalid attempt to destructure non-iterable instance");
            }
        };
    }();

    /** function initializing custom bindings */
    function bindings(options, action, viewCore) {

        /**
         * Add class "-active" to necessary node
         * @type {{update: (function())}}
         */
        _knockout2.default.bindingHandlers.activeElement = {
            update: function update(element, valueAccessor) {
                var value = valueAccessor(),
                    target = void 0,
                    accestors = void 0;
                if (value) {
                    target = element;
                    (0, _jquery2.default)(element).addClass('-active');
                    accestors = (0, _jquery2.default)(element).closest('.-level1').closest(options.itemClass);
                    (0, _jquery2.default)(accestors.last()).addClass('-active');
                }
            }
        };

        /**
         * Adds class "-level'i'" to nodes, where 'i' is nesting level
         * @type {{update: (function())}}
         */
        _knockout2.default.bindingHandlers.nestLevel = {
            update: function update(element, valueAccessor) {
                var value = valueAccessor(),
                    string = '-level' + value;
                (0, _jquery2.default)(element).addClass(string);
            }
        };

        /**
         * Toggles the additional link appearence for click action
         * @type {{update: (function())}}
         */
        _knockout2.default.bindingHandlers.toggleLink = {
            update: function update(element, valueAccessor, allBindings, viewModel) {
                var _valueAccessor = valueAccessor(),
                    _valueAccessor2 = _slicedToArray(_valueAccessor, 2),
                    action = _valueAccessor2[0],
                    childrenCount = _valueAccessor2[1],
                    menu = (0, _jquery2.default)(element).siblings().find(options.innerListsClass);

                if (viewModel.static) {
                    menu = menu[0];
                }
                if (!!childrenCount && action === 'click') {
                    viewCore.addlink(element, menu, options);
                }
                if (action === 'hover') {
                    (0, _jquery2.default)(menu).find('.-added').remove();
                }
            }
        };

        /**
         * Adds custom options to node
         * @type {{update: (function())}}
         */
        _knockout2.default.bindingHandlers.customOption = {
            update: function update(element, valueAccessor) {
                var value = valueAccessor();
                viewCore.customOptions(element, value);
            }
        };

        /**
         * Dynamicaly chose what node action is used and provide action binding
         * @type {{update: (function()), preprocess: (function())}}
         */
        _knockout2.default.bindingHandlers.setDynamicAction = {
            update: function update(element, valueAccessor, allBindings, viewModel) {
                var val = valueAccessor;
            },
            preprocess: function preprocess(value, name, addBinding) {
                var str = void 0,
                    action = options.action;
                str = value === '1' ? '{mouseenter: $root.toggleInner, mouseleave: $root._collapseAll}' : '{mouseenter: $root.toggleInner}';
                if (action === 'hover') {
                    addBinding('click', 'function (){  \n                    return true; \n                }');
                    addBinding('event', str);
                } else {
                    addBinding('click', '$root.toggleInner');
                    addBinding('event', '{}');
                }
            }
        };
    }
    module.exports = exports['default'];
});
