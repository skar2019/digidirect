define(['module', 'exports', 'jquery', './../common/store', 'matchMedia'], function (module, exports, _jquery, _store, _matchMedia) {
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

    var View = function () {
        function View(options) {
            _classCallCheck(this, View);

            this.options = Object.assign({}, this.options, options);
            this.watchers();
            this.bind();
        }

        /**
         * Watchers
         */


        _createClass(View, [{
            key: 'watchers',
            value: function watchers() {
                var _this = this;

                _store.Store.on(_store.Events.DATA_FETCH_PROGRESS, function () {
                    return _this.progress();
                });
                _store.Store.on(_store.Events.DATA_FETCH_SUCCESS, function (data) {
                    return _this.success(data);
                });
                _store.Store.on(_store.Events.FAQ_SEARCH, function (data) {
                    return _this.search(data);
                });
                _store.Store.on(_store.Events.FAQ_ITEM_TOGGLE, function (data) {
                    return _this.toggle(data);
                });
                _store.Store.on(_store.Events.FAQ_COMPACT_MODE_ON, function (data) {
                    return _this.onCompactMode(data);
                });
                _store.Store.on(_store.Events.FAQ_COMPACT_MODE_OFF, function (data) {
                    return _this.offCompactMode(data);
                });
                _store.Store.on(_store.Events.ERROR, function (data) {
                    return _this.error(data);
                });
            }
        }, {
            key: 'bind',
            value: function bind() {
                this.initSearch();
                this.initActions();
                this.toggleQuestion();
                this.goBack();
                this.initCompactMode();
                this.showAllTags();
                this.initLoadNextPage();
                this.initQuestionForm();
            }
        }, {
            key: 'initSearch',
            value: function initSearch() {
                (0, _jquery2.default)(this.options.searchForm).on('submit', function () {
                    _store.Store.emit(_store.Events.FAQ_SEARCH, (0, _jquery2.default)(this));
                    return false;
                });
            }
        }, {
            key: 'initActions',
            value: function initActions() {
                var _this2 = this;

                (0, _jquery2.default)(this.options.actionLinks).on('click', function (e) {
                    var $this = (0, _jquery2.default)(e.currentTarget),
                        url = $this.attr('href');
                    e.preventDefault();
                    if (url === undefined) {
                        url = _this2.options.baseUrl;
                    }
                    _this2.loadCategoryItems($this, url);
                });
            }
        }, {
            key: '_renderData',
            value: function _renderData(data) {
                (0, _jquery2.default)(this.options.list).html(data.content);
            }
        }, {
            key: 'search',
            value: function search(form) {
                if (form.valid()) {
                    this.goForward();
                    (0, _jquery2.default)(this.options.actionLinks).removeClass('-active');
                    var params = '?faqType=search' + '&faqId=' + (0, _jquery2.default)(this.options.searchField).val() + '&page=1';
                    _store.Store.emit(_store.Events.DATA_FETCH_START, this.options.url + params, this.options.baseUrl);
                }
            }
        }, {
            key: 'loadCategoryItems',
            value: function loadCategoryItems(category) {
                var realUrl = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

                var $this = category,
                    type = $this.data('type'),
                    categoryId = $this.data('category-id'),
                    page = $this.data('page');

                this.goForward();

                // Filling input:hidden
                (0, _jquery2.default)('#faqtype').value = $this.data('type');
                (0, _jquery2.default)('#faqid').value = $this.data('category-id');

                if (!$this.hasClass('-active')) {
                    (0, _jquery2.default)(this.options.actionLinks).removeClass('-active');
                    $this.addClass('-active');
                    var params = '?faqType=' + type + '&faqId=' + categoryId + '&page=' + page;
                    _store.Store.emit(_store.Events.DATA_FETCH_START, this.options.url + params, realUrl);
                }
            }
        }, {
            key: 'toggleQuestion',
            value: function toggleQuestion() {
                (0, _jquery2.default)(document).on('click', this.options.questions, function (e) {
                    e.preventDefault();
                    _store.Store.emit(_store.Events.FAQ_ITEM_TOGGLE, (0, _jquery2.default)(this).data('question-id'));
                });
            }
        }, {
            key: 'toggle',
            value: function toggle(id) {
                var $item = (0, _jquery2.default)('#faq-question-' + id).closest(this.options.items);

                if ($item.hasClass('-active')) {
                    $item.removeClass('-active');
                } else {
                    if (!this.options.multipleCollapsible) {
                        $item.siblings(this.options.items).removeClass('-active');
                    }
                    $item.addClass('-active');
                }
            }
        }, {
            key: 'goForward',
            value: function goForward() {
                (0, _jquery2.default)(this.options.container).addClass('-active');
            }
        }, {
            key: 'goBack',
            value: function goBack() {
                var _this3 = this;

                (0, _jquery2.default)(document).on('click', this.options.backItem, function () {
                    return _this3.goToCategories();
                });
            }
        }, {
            key: 'goToCategories',
            value: function goToCategories() {
                (0, _jquery2.default)(this.options.container).removeClass('-active');
            }
        }, {
            key: 'showAllTags',
            value: function showAllTags() {
                var _this4 = this;

                (0, _jquery2.default)(document).on('click', this.options.toggleTags, function (e) {
                    (0, _jquery2.default)(e.currentTarget).addClass('-hide');
                    (0, _jquery2.default)(_this4.options.extraTags).addClass('-show');
                });
            }
        }, {
            key: 'initLoadNextPage',
            value: function initLoadNextPage() {
                var _this5 = this;

                (0, _jquery2.default)(document).on('click', this.options.nextPage, function (e) {
                    e.preventDefault();
                    var urlParams = (0, _jquery2.default)(e.currentTarget).attr('href').split('?')[1],
                        realUrl = void 0,
                        pageParamName = 'p',
                        pageParamValue = void 0;

                    if (urlParams) {
                        urlParams = '?' + urlParams;
                    } else {
                        urlParams = '';
                    }

                    pageParamValue = _this5.getParameterByName(pageParamName, _this5.options.url + urlParams);

                    realUrl = _this5.removeEndSymbol(window.location.protocol + '//' + window.location.host + window.location.pathname, '/') + '?' + pageParamName + '=' + pageParamValue;

                    _store.Store.emit(_store.Events.DATA_FETCH_START, _this5.options.url + urlParams, realUrl);
                });
            }
        }, {
            key: 'getParameterByName',
            value: function getParameterByName(name, url) {
                var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                    results = regex.exec(url);
                if (!results) {
                    return null;
                }
                if (!results[2]) {
                    return '';
                }
                return decodeURIComponent(results[2].replace(/\+/g, ' '));
            }
        }, {
            key: 'removeEndSymbol',
            value: function removeEndSymbol(string, symbol) {
                var position = string.lastIndexOf(symbol);
                if (position == string.length - symbol.length) {
                    string = string.substr(0, position);
                }
                return string;
            }
        }, {
            key: 'initCompactMode',
            value: function initCompactMode() {
                if (this.options.compactModeBreakpoint) {
                    (0, _matchMedia2.default)({
                        media: '(min-width: ' + this.options.compactModeBreakpoint + ')',
                        entry: function entry() {
                            return _store.Store.emit(_store.Events.FAQ_COMPACT_MODE_OFF);
                        },
                        exit: function exit() {
                            return _store.Store.emit(_store.Events.FAQ_COMPACT_MODE_ON);
                        }
                    });
                }
            }
        }, {
            key: 'onCompactMode',
            value: function onCompactMode() {
                this.goToCategories();
            }
        }, {
            key: 'offCompactMode',
            value: function offCompactMode() {}
        }, {
            key: 'initQuestionForm',
            value: function initQuestionForm() {
                var _this6 = this;

                (0, _jquery2.default)(document).on('click', this.options.questionButton, function () {
                    (0, _jquery2.default)(_this6.options.questionContainer).addClass('-active');
                });
                (0, _jquery2.default)(this.options.questionForm).on('submit', function () {
                    if ((0, _jquery2.default)(_this6.options.questionForm).valid()) {
                        (0, _jquery2.default)(_this6.options.questionFormSubmit).attr('disabled', true);
                    }
                });
            }
        }, {
            key: 'progress',
            value: function progress() {
                (0, _jquery2.default)(this.options.container).loader().trigger('processStart');
            }
        }, {
            key: 'success',
            value: function success(data) {
                this._renderData(data);
                (0, _jquery2.default)(this.options.container).loader().trigger('processStop');
            }
        }, {
            key: 'error',
            value: function error() {}
        }]);

        return View;
    }();

    exports.default = View;
    module.exports = exports['default'];
});
