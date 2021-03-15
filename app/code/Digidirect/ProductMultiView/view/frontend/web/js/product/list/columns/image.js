define([
    'underscore',
    'Magento_Catalog/js/product/list/columns/image'
], function (_, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            alternativeImageCode: 'default',
            image: {}
        },

        /**
         * Find alternative image by code in scope of images
         *
         * @param {Object} images
         * @returns {*|T}
         */
        getAlternativeImage: function (images) {
            var alternativeImages = _.filter(images, function (image) {
                return this.alternativeImageCode === image.code;
            }, this);

            return alternativeImages.length ? alternativeImages.pop() : undefined;
        },

        /**
         * Get alternative image path.
         *
         * @param {Object} row
         * @return {String}
         */
        getAlternativeImageUrl: function (row) {
            return this.getAlternativeImage(row.images).url;
        },

        /**
         * Check if alternative image exist.
         *
         * @param {Object} row
         * @return {Boolean}
         */
        alternativeImageExists: function (row) {
            return typeof this.getAlternativeImage(row.images) !== 'undefined';
        }
    });
});
