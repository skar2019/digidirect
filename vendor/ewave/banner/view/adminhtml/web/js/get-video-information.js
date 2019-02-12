define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'jquery/ui'
], function ($, alert) {
    'use strict';

    $.widget('mage.videoData', {
        options: {
            eventSource: '' // where is data going from - focus out or click on button
        },

        _REQUEST_VIDEO_INFORMATION_TRIGGER: 'request_video_information',
        _ERROR_UPDATE_INFORMATION_TRIGGER: 'error_updated_information',
        _VIDEO_URL_VALIDATE_TRIGGER: 'validate_video_url',
        _NOTIFICATION: 'notify',

        /**
         * @private
         */
        _init: function () {
            this.element.on(this._VIDEO_URL_VALIDATE_TRIGGER, $.proxy(this._onUrlValidateHandler, this));
            this.element.on(this._NOTIFICATION, $.proxy(this._showNotification, this));
        },

        _showNotification: function (event, text) {
            alert({
                content: $.mage.__(text)
            });
        },

        /**
         * @private
         */
        _onUrlValidateHandler: function (event, callback) {
            callback();
        }
    });
});
