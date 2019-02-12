define(['module', 'exports', './actions/dynamic/bindings', 'jquery'], function (module, exports, _bindings, _jquery) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _bindings2 = _interopRequireDefault(_bindings);

    var _jquery2 = _interopRequireDefault(_jquery);

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

    var View = function () {
        /**
         * Initializing View
         * @param options
         */
        function View(options) {
            _classCallCheck(this, View);

            this.options = Object.assign({}, this.options, options);
            this.bind();
        }

        _createClass(View, [{
            key: 'bind',
            value: function bind() {
                if (this.isTouchDevice()) {
                    this.options.action = 'click';
                }
                this.loadAction();
            }
        }, {
            key: 'loadAction',
            value: function loadAction() {
                var _this = this;

                var actionString = void 0;
                actionString = this.options.static ? 'static' : 'dynamic';

                // download necessary action, depending on chosen option
                require(['./Ewave_Navigation/js/dist/views/actions/' + actionString + '/action'], function (Action) {
                    _this.action = new Action(_this.options, View);
                    if (!_this.options.static) {
                        (0, _bindings2.default)(_this.options, _this.action, View);
                    }
                    _this.loadExtraLogic();
                });
            }
        }, {
            key: 'loadExtraLogic',
            value: function loadExtraLogic() {
                // downloading and initializing of offcanvas logic if it was enabled
                if (this.options.responsive) {
                    this.loadOffCanvas();
                }
            }
        }, {
            key: 'loadOffCanvas',
            value: function loadOffCanvas() {
                var _this2 = this;

                var OFFCANVAS_PATH = 'Ewave_Navigation/js/dist/views/actions/common/offcanvas';
                require(['./' + OFFCANVAS_PATH], function (Offcanvas) {
                    _this2.offcanvas = new Offcanvas(_this2.options, _this2.action);
                });
            }
        }, {
            key: 'isTouchDevice',
            value: function isTouchDevice() {
                return 'ontouchstart' in window || navigator.msMaxTouchPoints > 0;
            }
        }], [{
            key: 'toggleInner',
            value: function toggleInner(event, options, self) {
                var target = (0, _jquery2.default)(event.target),
                    targetId = void 0,
                    $menu = target.closest(options.area),
                    $subMenu = void 0,
                    menuItem = void 0;

                if (target.not(options.itemLabelClass)) {
                    target = target.closest(options.itemLabelClass);
                }

                targetId = target.attr('id');
                $subMenu = $menu.find('[data-id="' + targetId + '"]');
                menuItem = target.closest(options.itemClass);

                menuItem.siblings(options.itemClass).removeClass('-open');
                menuItem.siblings(options.itemClass).find(options.subMenuBlockClass).removeClass('-open');

                if ($subMenu.length) {
                    event.preventDefault();
                    if ($subMenu.hasClass('-open')) {
                        menuItem.removeClass('-open');
                        $subMenu.removeClass('-open');
                    } else {
                        menuItem.addClass('-open');
                        $subMenu.addClass('-open');
                    }
                }

                event.stopPropagation();
            }
        }, {
            key: 'addLink',
            value: function addLink(element, menu, options) {
                var $element = (0, _jquery2.default)(element),
                    copiedLi = (0, _jquery2.default)($element.closest(options.itemClass)[0]).clone(),
                    copiedLiClasses = options.itemClass.replace('.', '') + ' -added ' + (0, _jquery2.default)(menu).closest(options.subMenuBlockClass).data('level-dip'),
                    copiedLink = document.createElement('a'),
                    str = options.linkString.replace('{original}', '' + $element.html());

                (0, _jquery2.default)(copiedLink).attr({
                    class: options.itemLabelClass.replace('.', ''),
                    href: $element.attr('href'),
                    title: (0, _jquery2.default)('<p>' + str + '</p>').text()
                }).html(str);
                copiedLi.removeAttr('class').addClass(copiedLiClasses);
                copiedLi.html(copiedLink);

                // insert link to top or bottom of sub-menu, depending on option
                if (options.addLinkToTop) {
                    copiedLi.prependTo((0, _jquery2.default)(menu));
                } else {
                    copiedLi.appendTo((0, _jquery2.default)(menu));
                }
            }
        }, {
            key: '_collapseAll',
            value: function _collapseAll(event, options) {
                var $target = (0, _jquery2.default)(event.target),
                    $item = void 0;

                if ($target.hasClass(options.itemClass)) {
                    $item = $target;
                } else {
                    $item = $target.closest(options.itemClass);
                }

                $item.removeClass('-open');
                $item.find(options.subMenuBlockClass).removeClass('-open');
            }
        }, {
            key: 'customOptions',
            value: function customOptions(element, values, options) {
                var str = '';
                values.forEach(function (property, index) {
                    // if property name is class we add to node it's value
                    if (property['option_name'] == 'class') {
                        str += ' ' + property['value'];
                    } else {
                        // else we treat it like css property and use like it
                        if (options.static) {
                            (0, _jquery2.default)(element).children(options.itemLabelClass).css(property['option_name'], property['value']);
                        } else {
                            (0, _jquery2.default)(element).css(property['option_name'], property['value']);
                        }
                    }
                });
                (0, _jquery2.default)(element).addClass(str);
            }
        }]);

        return View;
    }();

    exports.default = View;
    ;
    module.exports = exports['default'];
});
