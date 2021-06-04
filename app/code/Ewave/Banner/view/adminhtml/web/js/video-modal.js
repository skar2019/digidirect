define([
    'jquery',
    'bannerGallery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/backend/tree-suggest',
    'mage/backend/validation',
    'newBannerVideoDialog'
], function ($, bannerGallery) {
    'use strict';

    $.widget('mage.bannerGallery', bannerGallery, {

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            var events = {},
                itemId;

            this._super();

            /**
             * Add item_id value to opened modal
             * @param {Object} event
             */
            events['click ' + this.options.imageSelector] = function (event) {
                if (!$(event.currentTarget).is('.ui-sortable-helper')) {
                    itemId = $(event.currentTarget).find('input')[0].name.match(/\[([^\]]*)\]/g)[1];
                    this.videoDialog.find('#item_id').val(itemId);
                }
            };
            this._on(events);
            this.element.prev().find('[data-role="add-video-button"]').on('click', this.showModal.bind(this));
            this.element.on('openDialog', '.gallery.ui-sortable', $.proxy(this._onOpenDialog, this));
        },

        /**
         * @private
         */
        _create: function () {
            this._super();
            this.videoDialog = this.element.find('#new-video');
            this.videoDialog.mage('newBannerVideoDialog', this.videoDialog.data('modalInfo'));
        },

        /**
         * Open dialog for external video
         * @private
         */
        _onOpenDialog: function (e, imageData) {
            if (imageData['media_type'] !== 'external-video') {
                this._superApply(arguments);
            } else {
                this.showModal();
            }
        },

        /**
         * Fired on trigger "openModal"
         */
        showModal: function () {
            this.videoDialog.modal('openModal');
        }
    });

    $.widget('mage.bannerGallery', $.mage.bannerGallery, {

        maxVideoQty: 1,
        addVideoButtonElement: '#add_video_button',
        addedVideoElements: '.item.video-item:not(.removed)',
        externalVideoType: 'external-video',
        videoItemClass: 'video-item',

        /**
         * Remove Image
         * @param {jQuery.Event} event
         * @param imageData
         * @private
         */
        _removeItem: function (event, imageData) {
            this._super(event, imageData);
            this._addVideoButtonManagement();
        },

        /**
         * Add image
         * @param event
         * @param imageData
         * @private
         */
        _addItem: function (event, imageData) {
            var data = this._super(event, imageData);

            if (data.imageData.media_type == this.externalVideoType) {
                data.element.addClass(this.videoItemClass);
            }

            this._addVideoButtonManagement();
        },

        /**
         * Don't allow adding more than maxVideoQty
         * @private
         */
        _addVideoButtonManagement: function () {
            var addedVideos = $(this.addedVideoElements);
            if (addedVideos && addedVideos.length >= this.maxVideoQty) {
                $(this.addVideoButtonElement).attr('disabled', 'disabled');
                return false;
            }
            $(this.addVideoButtonElement).removeAttr('disabled');
        }
    });

    return $.mage.bannerGallery;
});
