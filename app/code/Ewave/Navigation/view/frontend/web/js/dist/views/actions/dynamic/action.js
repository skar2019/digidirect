define(['module', 'exports', 'jquery', 'knockout'], function (module, exports, _jquery, _knockout) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    var _jquery2 = _interopRequireDefault(_jquery);

    var _knockout2 = _interopRequireDefault(_knockout);

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

    var Action = function () {
        /**
         * Initialize component's viewmodel
         * @param options
         * @param viewCore
         */
        function Action(options, viewCore) {
            _classCallCheck(this, Action);

            var self = this;
            this.options = Object.assign({}, this.options, options);

            this.viewModel = function (options) {
                var _this = this;

                this.wrapperClass = _knockout2.default.observable((options.horizontal ? ' -horizontal' : '') + ' ' + (options.expanded ? ' -expanded' : ''));

                (0, _jquery2.default)(options.area).addClass(options.responsive ? '-responsive' : '');

                this.menu = options['menu'];
                this.action = options['action'];
                this.responsive = options['responsive'];


                this.action = _knockout2.default.observable(this.action);

                this.toggleInner = function (item, event) {
                    viewCore.toggleInner(event, _this.options, _this);
                    var target = (0, _jquery2.default)(event.target);
                    if (!(!!target.siblings().find(options.innerListsClass).length || !!target.siblings().find(options.cmsBlockClassName).length)) {
                        return true;
                    }
                };

                this._collapseAll = viewCore._collapseAll;

                this._isLink = function (item) {
                    return item['is_link'] === true || item['is_link'] === '1';
                };

                this.displayMode = function (item) {
                    return _this._isLink(item) ? 'Ewave_Navigation/common/link' : 'Ewave_Navigation/common/span';
                };
            };

            this.vm = new this.viewModel(self.options);

            if (this.options.action === 'click') {
                (0, _jquery2.default)(window).on('click', function () {
                    (0, _jquery2.default)(options.subMenuBlockClass + '.-open').removeClass('-open');
                });
            }

            this._renderTemplate();
        }

        /**
         * renders knockout template in selected area and binds viewmodel to this area
         * @private
         */


        _createClass(Action, [{
            key: '_renderTemplate',
            value: function _renderTemplate() {
                var _this2 = this;

                var self = this;
                var template = void 0,
                    path = 'text!Ewave_Navigation/template/view/action.html';
                try {
                    require([path], function (action) {
                        template = action;
                        (0, _jquery2.default)(template).appendTo(_this2.options.area);
                        _knockout2.default.applyBindings(self.vm, (0, _jquery2.default)(_this2.options.area)[0]);
                    });
                } catch (e) {
                    console.warn("Navigation: Template hasn't been loaded");
                }
            }
        }]);

        return Action;
    }();

    exports.default = Action;
    module.exports = exports['default'];
});
