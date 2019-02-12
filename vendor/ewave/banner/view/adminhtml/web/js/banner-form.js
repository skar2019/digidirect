define([
    'jquery',
    'Magento_Ui/js/form/form'
], function ($, Form) {
    'use strict';

    return Form.extend({
        defaults: {
            bannerImagesSelector: '#banner_images_gallery__content'
        },

        /**
         * Validate and save form.
         *
         * @param {String} redirect
         * @param {Object} data
         */
        save: function (redirect, data) {

            /**
             * prepare hidden banner images to send
             */
            $(this.bannerImagesSelector).find('input[type=hidden]').each(function (i, elem) {
                data[$(elem).attr('name')] = $(elem).val();
            });
            this._super(redirect, data);
        }
    });
});
