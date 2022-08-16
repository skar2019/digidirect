define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        initialize: function () {
            this._super();
            this.toggleBlockDisplay();
        },
        toggleBlockDisplay: function () {
            alert('New JS File Works!');
        }
    });
});
