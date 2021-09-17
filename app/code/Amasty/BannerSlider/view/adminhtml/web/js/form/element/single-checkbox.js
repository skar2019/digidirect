/**
 * @api
 */

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'rjsResolver'
], function (Checkbox, resolver) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            modules: {
                resizeImages: 'index = resize_images',
                mobileWidth: 'index = mobile_width',
                mobileHeight: 'index = mobile_height'
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            var self = this;

            this._super();

            resolver(function () {
                self.resizeImages = self.resizeImages();
                self.mobileWidth = self.mobileWidth();
                self.mobileHeight = self.mobileHeight();

                if (+self.value() && +self.resizeImages.value()) {
                    self._toggleMobileOptions(false);
                }

                self._initValueObserver();
            });

            return this;
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            if (+this.resizeImages.value()) {
                this._toggleMobileOptions(!+this.value());
            }

            return this._super();
        },

        /**
         * @private
         * @returns {void}
         */
        _initValueObserver: function () {
            var self = this;

            this.resizeImages.value.subscribe(function () {
                if (+self.value()) {
                    self._toggleMobileOptions(false);
                }
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _toggleMobileOptions: function (value) {
            this.mobileWidth.visible(value);
            this.mobileHeight.visible(value);
        }
    });
});
