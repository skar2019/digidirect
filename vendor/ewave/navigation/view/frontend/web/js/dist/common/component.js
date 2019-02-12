define(['module', 'exports', 'ewaveUtils'], function (module, exports, _ewaveUtils) {
    'use strict';

    Object.defineProperty(exports, "__esModule", {
        value: true
    });

    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }

    var Component =
    /**
     * Create a component instatnce
     * @param options
     */
    function Component(options) {
        _classCallCheck(this, Component);

        this.options = Object.assign({}, this.options, options);
        (0, _ewaveUtils.loadView)(this.options, this, 'Ewave_Navigation/js/dist/views/index', 'Navigation');
    };

    exports.default = Component;
    module.exports = exports['default'];
});
