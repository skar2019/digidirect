define(['module', 'exports', 'jquery'], function (module, exports, $) {
    'use strict';

    var MIXIN = {
        
        checkoutEvent: function checkoutEvent() {
            
            console.log("Test Trigger On checkoutEvent()!");
            
            /*function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            var customerId = getCookie('PAC');
            var sessionId = getCookie('pa_session_id');
            var currentUrl = window.location.href;

            var configCustomerId;
            var configSessionId;

            configCustomerId = customerId;
            configSessionId = sessionId;
            
            var widgetId = '631dfdd4-dc01-ef11-abf3-02bf4bf6447c';

            var date = new Date();
            var now_utc = Date.UTC(date.getUTCFullYear(), date.getUTCMonth(),
                    date.getUTCDate(), date.getUTCHours(),
                    date.getUTCMinutes(), date.getUTCSeconds());
            var products = [];

            $("#mini-cart .product-item").each(function() {

                var productItem;

                var refId = $(this).find("[pa-option-label='refId']").attr("pa-option-value"); 
                var quantity = $(this).find(".cart-item-qty").attr("data-item-qty"); 
                var routeId = $(this).find("[pa-option-label='routeId']").attr("pa-option-value"); 
                var widgetId = $(this).find("[pa-option-label='widgetId']").attr("pa-option-value"); 

                productItem = {"refId": refId, "quantity": quantity, "routeId": routeId, "widgetId": widgetId};
                products.push(productItem);

            });

            const checkoutData = {
                //customerId: configCustomerId,
                //sessionId: configSessionId,
                events: [
                    {
                        currentUrl: currentUrl,
                        eventTime: date.toISOString(),
                        products: products,
                        widgetId: widgetId,
                        routeId: routeId
                    }
                ]
            };

            console.log("checkoutData", JSON.stringify(checkoutData));*/
        }
    };

    exports.default = function (target) {
        return target.extend(MIXIN);
    };

    module.exports = exports['default'];
    
});
