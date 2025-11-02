define(["jquery", "ko", "uiComponent"], function ($, ko, Component) {
    "use strict";

    return Component.extend({
        initialize: function () {
            //initialize parent Component
            this._super();
            this.qty = ko.observable(this.defaultQty);
        },

        decreaseQty: function () {
            $(".box-tocart").removeClass("error");
            $(".qty-error-message").hide();
            var newQty = this.qty() - 1;
            if (newQty < 1) {
                newQty = 1;
            }
            return this.qty(newQty);
        },

        increaseQty: function () {
            var cartTotalQty = Number($("#cart-total-qty").html());

            var maxQty = Number($(".qty-pdp").data("maxqty"));

            if (cartTotalQty + this.qty() >= maxQty) {
                alert("Cart quantity limit reached!");
            } else {
                var newQty = this.qty() + 1;
                if (newQty > maxQty) {
                    $(".box-tocart").addClass("error");
                    $(".qty-error-message").show();
                    return;
                } else {
                    return this.qty(newQty);
                }
            }
        },
    });
});
