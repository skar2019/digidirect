define(['module', 'exports', 'digidirectUtils'], function (module, exports, _digidirectUtils) {
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
        (0, _digidirectUtils.loadView)(this.options, this, 'Digidirect_Navigation/js/dist/views/index', 'Navigation');
    };

    exports.default = Component;
    module.exports = exports['default'];
});
