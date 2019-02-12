define(['module', 'exports', 'jquery', 'mage/template', 'slickFilter'], function (module, exports, _jquery, _template, _slickFilter) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _jquery2 = _interopRequireDefault(_jquery);

    var _template2 = _interopRequireDefault(_template);

    var _slickFilter2 = _interopRequireDefault(_slickFilter);

    function _interopRequireDefault(obj) {
        return obj && obj.__esModule ? obj : {
            default: obj
        };
    }

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
        function View(_ref) {
            var options = _ref.options,
                element = _ref.element,
                _store = _objectWithoutProperties(_ref, ['options', 'element']);

            _classCallCheck(this, View);

            this.options = Object.assign({}, this.options, options);
            this.element = element;
            this.Store = _store.Store;
            this.Events = _store.Events;
            this.previousSort = [];
            this.visibleClassName = '-visible';
            this.hiddenClassName = '-hidden';
            this.bind();
            this.watchers();
        }

        _createClass(View, [{
            key: 'watchers',
            value: function watchers() {
                var _this = this;

                this.Store.on(this.Events.RELATED_INITIALIZED, function (data) {
                    return _this.initialized(data);
                });
                this.Store.on(this.Events.OPEN_DETAILS, function (data) {
                    return _this.open(data);
                });
                this.Store.on(this.Events.OPENED_DETAILS, function (data) {
                    return _this.opened(data);
                });
                this.Store.on(this.Events.CLOSE_DETAILS, function (data) {
                    return _this.close(data);
                });
                this.Store.on(this.Events.CLOSED_DETAILS, function (data) {
                    return _this.closed(data);
                });
                this.Store.on(this.Events.TOGGLE_DETAILS, function (data) {
                    return _this.toggle(data);
                });
                this.Store.on(this.Events.FILTERED, function (data) {
                    return _this.filtered(data);
                });
            }
        }, {
            key: 'bind',
            value: function bind() {
                this.initializeFilters();
                this.initializeDetailsExpander();
                this.Store.emit(this.Events.RELATED_INITIALIZED, this.element);
            }
        }, {
            key: 'initializeFilters',
            value: function initializeFilters() {
                var self = this;

                if (this.options.filterable) {
                    self.setFilterItems();
                    self.items = self.getItems();
                }
            }
        }, {
            key: 'getItems',
            value: function getItems() {
                var items = this.element[0].querySelectorAll(this.options.itemSelector);
                return items;
            }
        }, {
            key: 'setHiddenClass',
            value: function setHiddenClass(element) {
                (0, _jquery2.default)(element).removeClass(this.visibleClassName).addClass(this.hiddenClassName);
                (0, _jquery2.default)(element).parents('.slick-slide').removeClass(this.visibleClassName).addClass(this.hiddenClassName);
            }
        }, {
            key: 'setVisibleClass',
            value: function setVisibleClass(element) {
                (0, _jquery2.default)(element).removeClass(this.hiddenClassName).addClass(this.visibleClassName);
                (0, _jquery2.default)(element).parents('.slick-slide').removeClass(this.hiddenClassName).addClass(this.visibleClassName);
            }
        }, {
            key: 'setFilterItems',
            value: function setFilterItems() {
                var _this2 = this;

                if (!this.options.filterItems) {
                    return;
                }
                (0, _jquery2.default)(this.options.filterItems).on('click', function (e) {
                    return _this2.applyFilter(e);
                });
            }
        }, {
            key: 'applyFilter',
            value: function applyFilter(e) {
                var element = e.currentTarget,
                    $element = (0, _jquery2.default)(element),
                    elementGroup = element.getAttribute('data-group'),
                    grid = document.getElementById(this.options.gridId);

                if (!$element.hasClass(this.options.filterActiveState)) {
                    this.Store.emit(this.Events.CLOSE_DETAILS, (0, _jquery2.default)(this.options.expanderElement + '.-active'));
                    $element.addClass(this.options.filterActiveState).siblings().removeClass(this.options.filterActiveState);
                    this.toggleFilterItemsInfo(elementGroup);
                    (0, _jquery2.default)(grid).trigger('filter:start');
                    this.filterItems(elementGroup);
                    (0, _jquery2.default)(grid).trigger('filter:done');
                    this.Store.emit(this.Events.FILTERED, $element);
                }
            }
        }, {
            key: 'filterItems',
            value: function filterItems(group) {
                var self = this;

                this.items.forEach(function (item) {
                    var categoties = item.getAttribute('data-groups');
                    if (categoties.indexOf(group) !== -1) {
                        self.setVisibleClass(item);
                    } else {
                        self.setHiddenClass(item);
                    }
                });
            }
        }, {
            key: 'toggleFilterItemsInfo',
            value: function toggleFilterItemsInfo(categoryId) {
                (0, _jquery2.default)(this.options.filterInfo + '[data-category-id="' + categoryId + '"]').addClass(this.options.filterActiveState).siblings('[data-category-id]').removeClass(this.options.filterActiveState);
            }
        }, {
            key: 'isEqualArrays',
            value: function isEqualArrays(a, b) {
                if (a.length != b.length) {
                    return false;
                }
                for (var i = 0; i < a.length; ++i) {
                    if (a[i] !== b[i]) {
                        return false;
                    }
                }
                return true;
            }
        }, {
            key: 'filtered',
            value: function filtered($element) {}
        }, {
            key: 'initializeDetailsExpander',
            value: function initializeDetailsExpander() {
                var _this3 = this;

                if (this.options.expandable) {
                    (0, _jquery2.default)(document).on('click', this.options.expanderElement, function (e) {
                        e.preventDefault();
                        _this3.Store.emit(_this3.Events.TOGGLE_DETAILS, (0, _jquery2.default)(e.currentTarget));
                    });
                }
            }
        }, {
            key: 'initialized',
            value: function initialized($element) {}
        }, {
            key: 'toggle',
            value: function toggle($element, state) {
                if ($element.hasClass('-active')) {
                    this.Store.emit(this.Events.CLOSE_DETAILS, $element);
                } else {
                    this.Store.emit(this.Events.OPEN_DETAILS, $element);
                }
            }
        }, {
            key: 'updateSwitchText',
            value: function updateSwitchText(element) {
                var moreText = element.data('text-more'),
                    lessText = element.data('text-less');

                if (element.hasClass('-active')) {
                    element.attr('title', lessText);
                    element.text(lessText);
                } else {
                    element.attr('title', moreText);
                    element.text(moreText);
                }
            }
        }, {
            key: 'open',
            value: function open($element) {
                if (!(0, _jquery2.default)($element.data('expander')).length) {
                    console.warn('Expander container not found!');
                    return;
                }

                var $previous = $element.siblings('.-active');

                this.removeContent($element);
                $element.addClass('-active');
                $element.find(this.options.switchElement).addClass('-active');
                $previous.removeClass('-active').find(this.options.switchElement).removeClass('-active');
                if (this.options.switchElement) {
                    this.updateSwitchText($previous.find(this.options.switchElement));
                    this.updateSwitchText($element.find(this.options.switchElement));
                }
                this.addContent($element);
                if (this.options.scroll) {
                    this.scroll($element);
                }
                this.Store.emit(this.Events.OPENED_DETAILS, $element);
            }
        }, {
            key: 'opened',
            value: function opened() {}
        }, {
            key: 'close',
            value: function close($element) {
                this.removeContent($element);
                $element.removeClass('-active');
                $element.find(this.options.switchElement).removeClass('-active');
                if (this.options.switchElement) {
                    this.updateSwitchText($element.find(this.options.switchElement));
                }
                this.Store.emit(this.Events.CLOSED_DETAILS, $element);
            }
        }, {
            key: 'closed',
            value: function closed() {}
        }, {
            key: 'addContent',
            value: function addContent($element) {
                var expanderTemplate = (0, _template2.default)(this.options.expanderTemplate);

                (0, _jquery2.default)(expanderTemplate({
                    data: {
                        html: (0, _jquery2.default)($element.data('expander')).html()
                    }
                })).insertAfter($element);
            }
        }, {
            key: 'removeContent',
            value: function removeContent($element) {
                $element.siblings(this.options.expanderHolder).remove();
            }
        }, {
            key: 'scroll',
            value: function scroll($element) {
                var offset = this.options.scrollTo === 'details' ? (0, _jquery2.default)(this.options.expanderHolder).offset().top : $element.offset().top;
                if (offset) {
                    (0, _jquery2.default)('html, body').animate({
                        scrollTop: offset
                    }, 500);
                }
            }
        }]);

        return View;
    }();

    exports.default = View;
    module.exports = exports['default'];
});
