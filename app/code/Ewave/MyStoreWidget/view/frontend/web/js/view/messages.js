define([
    'Magento_Ui/js/view/messages',
    'Ewave_MyStoreWidget/js/model/messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({
        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
