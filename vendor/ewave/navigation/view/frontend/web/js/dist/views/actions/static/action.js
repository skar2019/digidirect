define(['module', 'exports', 'jquery'], function (module, exports, _jquery) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

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

    var Action =
    /**
     * Initialize static functionality
     * @param options
     * @param viewCore
     */
    function Action(options, viewCore) {
        var _this = this;

        _classCallCheck(this, Action);

        this._initAdditional = function () {
            _this._activeLink();
            if (_this.options.action === 'click') {
                _this._addLinks();
            }
        };

        this._initAction = function () {
            var items = (0, _jquery2.default)(_this.options.wrapperClass + ' ' + _this.options.itemClass),
                self = _this;
            if (_this.options.action === 'hover') {
                items.on('mouseenter', _this.toggleInner);
                items.on('mouseleave', function (e) {
                    self.viewCore._collapseAll(e, self.options);
                });
                (0, _jquery2.default)(_this.options.wrapperClass).on('mouseleave', function () {
                    self._collapseSub();
                });
            } else {
                items.on('click', _this.toggleInner);
                (0, _jquery2.default)(window).on('click', _this._collapseSub);
            }
        };

        this._toggleAction = function (action) {
            var items = (0, _jquery2.default)(_this.options.wrapperClass + ' ' + _this.options.itemClass);
            if (action === 'click') {
                _this.options.action = 'click';
                items.off('mouseenter', _this.toggleInner);
                items.off('mouseleave');
                (0, _jquery2.default)(_this.options.wrapperClass).off('mouseleave');
                _this._addLinks();
            } else {
                _this.options.action = 'hover';
                items.off('click', _this.toggleInner);
                (0, _jquery2.default)(window).off('click', _this._collapseSub);
                _this._removeAdditionalLinks();
            }

            _this._initAction();
        };

        this.toggleInner = function (event) {
            _this.viewCore.toggleInner(event, _this.options, _this);
        };

        this._activeLink = function () {
            var element = (0, _jquery2.default)(_this.options.wrapperClass + ' ' + _this.options.itemClass + '.-active'),
                accestors = element.closest('.-level1').closest(_this.options.itemClass);
            accestors.addClass('-active');
        };

        this._addLinks = function () {
            var links = (0, _jquery2.default)(_this.options.wrapperClass + ' .-parent > a' + _this.options.itemLabelClass),
                menu = void 0;
            if (_this.options.linkString) {
                links.each(function (index, element) {
                    menu = (0, _jquery2.default)(element).siblings().find(_this.options.innerListsClass)[0];
                    _this.viewCore.addLink(element, menu, _this.options);
                });
            }
        };

        this._removeAdditionalLinks = function () {
            (0, _jquery2.default)(_this.options.itemClass + '.-added').remove();
        };

        this._collapseSub = function () {
            (0, _jquery2.default)(_this.options.wrapperClass + ' ' + _this.options.itemClass + '.-open').removeClass('-open');
            (0, _jquery2.default)(_this.options.subMenuBlockClass + '.-open').removeClass('-open');
        };

        this.options = Object.assign({}, this.options, options);
        this.viewCore = viewCore;
        this._initAdditional();
        this._initAction();
    }

    /**
     * Triggers functions that defines additional classes and functionality for menu nodes
     * @private
     */


    /**
     * Select what action will be used and bind it with nodes
     * @private
     */

    /**
     * Toggles actions: click -> hover, hover -> click
     * @private
     */


    /**
     * Toggle inner sub-menus
     * @param event
     */


    /**
     * Add class "-active" to necessary node
     * @private
     */


    /**
     * Add aditional link to sub-menus, used only for click action
     * @private
     */


    /**
     * Remove links with class -added
     * @private
     */
    ;

    exports.default = Action;
    module.exports = exports['default'];
});
