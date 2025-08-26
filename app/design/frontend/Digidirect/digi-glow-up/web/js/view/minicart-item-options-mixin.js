define([], function () {
    'use strict';

    return function (Renderer) {
        return Renderer.extend({
            /**
             * Get product custom options for an item
             */
            getItemOptions: function (item) {
                if (item.options && item.options.length) {
                    return item.options;
                }
                return [];
            }
        });
    };
});
