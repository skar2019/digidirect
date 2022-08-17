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
            //alert('New JS File Works!');
            
            
            $(".locator-list").bind("DOMSubtreeModified", function() { 
                if ($('#pickup-available .collectlocator-wrapper .locator-items .locator-item').length != 0) {
                    alert("#pickup-available length is " + $('#pickup-available .collectlocator-wrapper .locator-items .locator-item').length);
                    //$("#pickup-available").removeAttr("style");
                    //$("#pickup-available").attr("style", "display:none !important;");
                }
                if ($('#pickup-unavailable .collectlocator-wrapper .locator-items .locator-item').length != 0) {
                    alert("#pickup-unavailable length is " + $('#pickup-unavailable .collectlocator-wrapper .locator-items .locator-item').length);
                    //$("#pickup-available").removeAttr("style");
                    //$("#pickup-available").attr("style", "display:none !important;");
                }
            });
            
        }
    });
});
