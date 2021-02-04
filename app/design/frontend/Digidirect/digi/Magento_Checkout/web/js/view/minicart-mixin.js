define(['jquery', 'ko'], function ($) {
    'use strict';

    return function (Component) {
        return Component.extend({
            /**
             * @returns {*}
             */
            initialize: function () {
                this._super();

                this.element = $('[data-block="minicart"]');

                this.setupAddToCartListener();

                return this;
            },

            /**
             * Set up listeners for adding to cart and updating cart.
             * Open minicart once cart is fully updated.
             */
            setupAddToCartListener: function () {
                var self = this;

                this.element.on('contentLoading', function () {
                    self.element.on('contentUpdated', function () {
                        self.openMinicart();
                    });
                });
            },

            /**
             * Open minicart.
             */
            openMinicart: function () {
                this.element.find('[data-role="dropdownDialog"]').dropdownDialog('open');
            }
        });
    }
});