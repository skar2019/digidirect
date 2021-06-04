/* eslint new-parens: [1], no-useless-call: [1] */
/* global FileReader */
define([
    'jquery',
    'underscore',
    'jquery/ui',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'mage/backend/tree-suggest',
    'mage/backend/validation',
    'Ewave_Banner/js/get-video-information',
    'loader'
], function ($, _) {
    'use strict';

    $.widget('mage.newBannerVideoDialog', {

        _previewImage: null,
        _previewVideo: null,
        _images: {},
        _imageTypes: [],
        _videoTypes: [
            '.mp4'
        ],
        _placeholder: null,
        _imageBannerGalleryWrapperSelector: '#image-container',
        _videoPreviewInputSelector: '#new_video_screenshot',
        _newVideoFileSelector: '#new_video_file',
        _videoDisableinputSelector: '#new_video_disabled',
        _videoPreviewImagePointer: '#new_video_screenshot_preview',
        _videoPreviewVideoPointer: '#new_video_video_preview',
        _videoPreviewElement: '#video_preview_element',
        _videoFormSelector: '#new_video_form',
        _itemIdSelector: '#item_id',
        _videoUrlSelector: '[name="video_title"]',
        _videoImageFilenameselector: '#file_name',
        _videoUrlWidget: null,
        _videoInformationBtnSelector: '[name="new_video_get"]',
        _editVideoBtnSelector: '.image',
        _deleteGalleryVideoSelector: '[data-role=delete-button]',
        _deleteGalleryVideoSelectorBtn: null,
        _videoInformationGetUrlField: null,
        _videoInformationGetEditBtn: null,
        _isEditPage: false,
        _tempPreviewImageData: null,
        _tempPreviewVideoData: null,
        _videoPlayerSelector: '.mage-new-video-dialog',
        _gallery: null,
        _previouslyUploadedImage: null,
        _sourceElement: null,
        _tmpVideoUrl: '/media/tmp/banner/original',

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            var events = {
                'setImage': '_onSetImage'
            };
            this._on(events);

            this._videoUrlWidget = this.element.find(this._videoUrlSelector).videoData({
                eventSource: 'focusout'
            });

            this._videoInformationGetUrlField = this.element.find(this._videoUrlSelector);
            this._videoInformationGetEditBtn = this._gallery.find(this._editVideoBtnSelector);
        },

        toggleSpeedField: function () {
            var playVideoAutomaticallyDesktop = this.element.find('#play_video_automatically_for_desktop'),
                playVideoAutomaticallyMobile = this.element.find('#play_video_automatically_for_mobile'),
                element = this.element.find('#play_video_after'),
                mobile = playVideoAutomaticallyMobile.val(),
                desktop = playVideoAutomaticallyDesktop.val(),
                value = mobile == 1 || desktop == 1;
            if (value == 1) {
                element.show();
                element.parents('.admin__field:first').show();
            } else {
                element.hide();
                element.parents('.admin__field:first').hide();
            }
        },

        /**
         * Fired when user click Edit Video button
         * @private
         */
        _onGetVideoInformationEditClick: function () {
            this._isEditPage = true;
        },

        /**
         * Remove ".tmp"
         * @param {String} name
         * @returns {*}
         * @private
         */
        __prepareFilename: function (name) {
            var tmppost = '.tmp';
            if (!name) {
                return name;
            }
            if (name.endsWith(tmppost)) {
                name = name.slice(0, name.length - tmppost.length);
            }
            return name;
        },

        /**
         * Set image data
         * @param {String} file
         * @param {Object} imageData
         * @private
         */
        _setImage: function (file, imageData) {
            file = this.__prepareFilename(file);
            this._images[file] = imageData;
            this._gallery.trigger('addItem', imageData);
            this.element.trigger('setImage', imageData);
            this._addVideoClass(imageData.url);
            if (typeof imageData.video_url == 'undefined') {
                imageData.video_url = this._tmpVideoUrl + file;
            }
        },

        /**
         * Get image data
         *
         * @param {String} file
         * @returns {*}
         * @private
         */
        _getImage: function (file) {
            file = this.__prepareFilename(file);
            return this._images[file];
        },

        /**
         * Replace image (update)
         * @param {String} oldFile
         * @param {String} newFile
         * @param {Object} imageData
         * @private
         */
        _replaceImage: function (oldFile, newFile, imageData) {
            var tmpNewFile = newFile,
                tmpOldImage,
                newImageId,
                oldNewFilePosition,
                fc,
                suff,
                searchsuff,
                key,
                oldValIdElem;

            oldFile = this.__prepareFilename(oldFile);
            newFile = this.__prepareFilename(newFile);
            tmpOldImage = this._images[oldFile];

            if (newFile === oldFile) {
                imageData.video_file = this._updateVideo();
                this._images[newFile] = imageData;
                this.saveImageRoles(imageData);

                return null;
            }

            imageData.video_file = this._updateVideo();

            this._removeImage(oldFile);
            $('.item.active.removed').remove();

            imageData.video_url = this._tmpVideoUrl + newFile;
            this._setImage(imageData.file, imageData);

            if (!oldFile || !imageData.oldFile) {
                return null;
            }

            newImageId = this.findElementId(tmpNewFile);
            fc = this.element.find(this._itemIdSelector).val();

            suff = 'banner_images[images]' + fc;

            searchsuff = 'input[name="' + suff + '[value_id]"]';
            key = this._gallery.find(searchsuff).val();

            if (!key) {
                return null;
            }

            oldValIdElem = document.createElement('input');
            this._gallery.find('form[data-form="edit-banner"]').append(oldValIdElem);
            $(oldValIdElem).attr({
                type: 'hidden',
                name: 'banner_images[images][' + newImageId + '][save_data_from]'
            }).val(key);

            oldNewFilePosition = parseInt(tmpOldImage.position, 10);
            imageData.position = oldNewFilePosition;

            this._gallery.trigger('setPosition', {
                imageData: imageData,
                position: oldNewFilePosition
            });
        },

        /**
         * Remove image data
         * @param {String} file
         * @private
         */
        _removeImage: function (file) {
            var imageData = this._getImage(file);

            if (!imageData) {
                return null;
            }

            this._gallery.trigger('removeItem', imageData);
            this.element.trigger('removeImage', imageData);
            delete this._images[file];
        },

        /**
         * Fired when image setted
         * @param {Event} event
         * @param {Object} imageData
         * @private
         */
        _onSetImage: function (event, imageData) {
            this.saveImageRoles(imageData);
        },

        /**
         *
         * Wrap _uploadFile
         * @param {String} file
         * @param {String} oldFile
         * @param {Function} callback
         * @private
         */
        _uploadVideo: function (file, oldFile, callback) {
            var url = this.options.saveVideoUrl,
                data = {
                    files: file,
                    url: url
                };

            $('#html-body').loader('show');
            this._uploadFile(data, $.proxy(function (result) {
                this._previouslyUploadedImage = result;
                this._onVideoLoaded(result, file, oldFile, callback);
                this._blockActionButtons(false);
                $('#html-body').loader('hide');
            }, this));
        },

        /**
         *
         * Wrap _uploadFile
         * @param {String} file
         * @param {String} oldFile
         * @param {Function} callback
         * @private
         */
        _uploadImage: function (file, oldFile, callback) {
            var url = this.options.saveImageUrl,
                data = {
                    files: file,
                    url: url
                };

            if (!file) {
                this._onImageLoaded(null, file, oldFile, callback);
                return;
            }
            this._uploadFile(data, $.proxy(function (result) {
                this._onImageLoaded(result, file, oldFile, callback);
                this._blockActionButtons(false);
            }, this));
        },

        /**
         * @param {String} result
         * @param {String} file
         * @param {String} oldFile
         * @param {Function} callback
         * @private
         */
        _onImageLoaded: function (result, file, oldFile, callback) {
            if (!result) {
                result = this._previouslyUploadedImage;
            }
            var data = JSON.parse(result),
                $video = this.element.find('video');

            if (this.element.find('#video_title').parent().find('.image-upload-error').length > 0) {
                this.element.find('.image-upload-error').remove();
            }

            if (data.errorcode || data.error) {
                this.element.find('#video_title').parent().append('<div class="image-upload-error">' +
                    '<div class="image-upload-error-cross"></div><span>' + data.error + '</span></div>');

                return;
            }

            $.each(this.element.find(this._videoFormSelector).serializeArray(), function (i, field) {
                if (field.name == 'video_roles[]') {
                    if (!data['video_roles']) {
                        data['video_roles'] = [field.value];
                        data['video_roles[]'] = [field.value];
                    } else {
                        data['video_roles'].push(field.value);
                        data['video_roles[]'].push(field.value);
                    }
                } else {
                    data[field.name] = field.value;
                }
            });
            data.disabled = this.element.find(this._videoDisableinputSelector).attr('checked') ? 1 : 0;
            data['media_type'] = 'external-video';
            data.oldFile = oldFile;
            data.video_file = this._updateVideo();

            if ($video.length) {
                data.video_width = $video[0].videoWidth;
                data.video_height = $video[0].videoHeight;
            }

            oldFile ? this._replaceImage(oldFile, data.file, data) : this._setImage(data.file, data);
            callback.call(0, data);
        },

        /**
         *
         * @returns {*}
         * @private
         */
        _updateVideo: function () {
            var previousFile = JSON.parse(this._previouslyUploadedImage);
            return previousFile ? previousFile.file : this.imageData.video_file ? this.imageData.video_file : null;
        },

        /**
         * @param {String} result
         * @param {String} file
         * @param {String} oldFile
         * @param {Function} callback
         * @private
         */
        _onVideoLoaded: function (result, file, oldFile, callback) {
            var data = JSON.parse(result);

            if (this.element.find('#video_title').parent().find('.image-upload-error').length > 0) {
                this.element.find('.image-upload-error').remove();
            }

            if (data.errorcode || data.error) {
                this.element.find('#video_title').parent().append('<div class="image-upload-error">' +
                    '<div class="image-upload-error-cross"></div><span>' + data.error + '</span></div>');

                return;
            }
            $.each(this.element.find(this._videoFormSelector).serializeArray(), function (i, field) {
                data[field.name] = field.value;
            });
            data.disabled = this.element.find(this._videoDisableinputSelector).attr('checked') ? 1 : 0;
            data['media_type'] = 'external-video';

            callback.call(0, data);
        },

        /**
         * File uploader
         * @private
         */
        _uploadFile: function (data, callback) {
            var fu = this.element.find(this._videoPreviewInputSelector),
                tmpInput = document.createElement('input'),
                fileUploader;

            $(tmpInput).attr({
                'name': fu.attr('name'),
                'value': fu.val(),
                'type': 'file',
                'data-ui-ud': fu.attr('data-ui-ud')
            }).css('display', 'none');
            fu.parent().append(tmpInput);
            fileUploader = $(tmpInput).fileupload();
            fileUploader.fileupload('send', data).success(function (result, textStatus, jqXHR) {
                tmpInput.remove();
                callback.call(null, result, textStatus, jqXHR);
            });
        },

        /**
         * Update style
         * @param {String} url
         * @private
         */
        _addVideoClass: function (url) {
            var classVideo = 'video-item';
            this._gallery.find('img[src="' + url + '"]').addClass(classVideo);
        },

        /**
         *  Image file input handler
         * @private
         */
        _onVideoInputChange: function () {
            var jFile = this.element.find(this._newVideoFileSelector),
                file = jFile[0],
                val = jFile.val(),
                prev = this._getPreviewImage(),
                ext = '.' + val.split('.').pop();

            if (!val) {
                return;
            }
            ext = ext ? ext.toLowerCase() : '';

            if (
                ext.length < 2 ||
                this._videoTypes.indexOf(ext.toLowerCase()) === -1 || !file.files || !file.files.length
            ) {
                prev.remove();
                jFile.val('');

                this._videoUrlWidget.trigger('notify', 'We do not recognize or support this file extension type');
                return;
            }
            file = file.files[0];
            this._tempPreviewVideoData = null;
            this._onVideoPreview(file, true);
        },

        /**
         * Change Preview
         * @param {String} src
         * @param {Boolean} local
         * @private
         */
        _onVideoPreview: function (src, local) {
            var video, renderImage, self = this;

            video = this._getPreviewVideo();

            /**
             * Callback
             * @param {String} source
             */
            renderImage = function (source) {
                if (!self._sourceElement) {
                    self._sourceElement = $(document.createElement('source'));
                }
                self._sourceElement.attr({
                    'type': 'video/mp4',
                    'src': source
                });

                video.attr({'controls': ''}).append(self._sourceElement).show();

                video.on('loadedmetadata', function () {
                    $('.item.video-item').find('.video_width').val(this.videoWidth);
                    $('.item.video-item').find('.video_height').val(this.videoHeight);

                    self._updateVideoDimensions(this.videoWidth, this.videoHeight);
                });
            };

            if (local) {
                this._readPreviewLocal(src, renderImage);
            } else {
                renderImage(src);
            }
        },

        _updateVideoDimensions: function (width, height) {
            var $dimens = $('.item.video-item').find('[data-role=image-dimens]');
            $dimens.text(width + 'x' + height + ' px');
        },

        /**
         * Return preview image instance
         * @returns {null}
         * @private
         */
        _getPreviewVideo: function () {
            if (this._previewVideo) {
                this._previewVideo.remove();
            }
            this._previewVideo = $(document.createElement('video'));
            this._previewVideo.css({
                'width': '100%',
                'display': 'none',
                'src': ''
            });
            $(this._previewVideo).insertAfter(this.element.find(this._videoPreviewVideoPointer));
            return this._previewVideo;
        },

        /**
         * Build widget
         * @private
         */
        _create: function () {
            var imgs = _.values(this.element.closest(this.options.videoSelector).data('images')) || [],
                widget,
                uploader,
                tmp,
                i,
                self = this,
                playVideoAutomaticallyDesktop = this.element.find('#play_video_automatically_for_desktop'),
                playVideoAutomaticallyMobile = this.element.find('#play_video_automatically_for_mobile'),
                videoUploader;

            this._imageTypes = this.options.previewImageAllowedExtensions;
            this._placeholder = this.options.placeholder;

            this._gallery = this.element.closest(this.options.videoSelector);

            for (i = 0; i < imgs.length; i++) {
                tmp = imgs[i];
                this._images[tmp.file] = tmp;

                if (tmp['media_type'] === 'external-video') {
                    tmp.subclass = 'video-item';
                    this._addVideoClass(tmp.url);
                }
            }

            this._gallery.on('openDialog', $.proxy(this._onOpenDialog, this));
            this._bind();
            this.createVideoItemIcons();
            widget = this;
            uploader = this.element.find(this._videoPreviewInputSelector);
            uploader.on('change', this._onImageInputChange.bind(this));
            uploader.attr('accept', this._imageTypes.join(','));

            videoUploader = this.element.find('#new_video_file');
            videoUploader.on('change', this._onVideoInputChange.bind(this));

            this.element.modal({
                type: 'slide',
                // appendTo: this._gallery,
                modalClass: 'mage-new-video-dialog form-inline',
                title: $.mage.__('New Video'),
                buttons: [
                    {
                        text: $.mage.__('Save'),
                        class: 'action-primary video-create-button',
                        click: $.proxy(widget._onCreate, widget)
                    },
                    {
                        text: $.mage.__('Cancel'),
                        class: 'video-cancel-button',
                        click: $.proxy(widget._onCancel, widget)
                    },
                    {
                        text: $.mage.__('Delete'),
                        class: 'video-delete-button',
                        click: $.proxy(widget._onDelete, widget)
                    },
                    {
                        text: $.mage.__('Save'),
                        class: 'action-primary video-edit',
                        click: $.proxy(widget._onUpdate, widget)
                    }
                ],

                /**
                 * @returns {null}
                 */
                opened: function () {
                    var roles,
                        file,
                        modalTitleElement,
                        imageData,
                        modal = widget.element.closest('.mage-new-video-dialog');

                    widget.element.find('#video_title').focus();
                    roles = widget.element.find('.video_image_role');
                    roles.prop('disabled', false);
                    file = widget.element.find('#file_name').val();
                    widget._onGetVideoInformationEditClick();
                    modalTitleElement = modal.find('.modal-title');

                    playVideoAutomaticallyDesktop.on('change', function (event) {
                        self.toggleSpeedField();
                    });
                    playVideoAutomaticallyMobile.on('change', function (event) {
                        self.toggleSpeedField();
                    });

                    self.toggleSpeedField();

                    if (!file) {
                        modal.find('.video-delete-button').hide();
                        modal.find('.video-edit').hide();
                        modal.find('.video-create-button').show();
                        roles.prop('checked', widget._gallery.find('.image.item:not(.removed)').length < 1);
                        modalTitleElement.text($.mage.__('New Video'));
                        widget._isEditPage = false;
                        return null;
                    }
                    widget._blockActionButtons(false);
                    modalTitleElement.text($.mage.__('Edit Video'));
                    widget._isEditPage = true;
                    imageData = widget._getImage(file);

                    if (!imageData) {
                        imageData = {
                            url: _.find(widget._gallery.find('.product-image'), function (image) {
                                return image.src.indexOf(file) > -1;
                            }).src
                        };
                    }

                    widget._onPreview(null, imageData.url, false);
                    widget._onVideoPreview(imageData.video_url, false);
                },

                /**
                 * Closed
                 */
                closed: function () {
                    widget._onClose();
                    widget.createVideoItemIcons();
                }
            });
            this.toggleButtons();
        },

        /**
         * @param {Boolean} status
         * @private
         */
        _blockActionButtons: function (status) {
            this.element
                .closest('.mage-new-video-dialog')
                .find('.page-actions-buttons button.video-create-button, .page-actions-buttons button.video-edit')
                .attr('disabled', status);
        },

        /**
         * Check form
         * @param {Function} callback
         */
        isValid: function (callback) {
            var videoForm = this.element.find(this._videoFormSelector),
                videoLoaded = true;

            this._videoUrlWidget.trigger('validate_video_url', $.proxy(function () {
                videoForm.mage('validation', {

                    /**
                     * @param {jQuery} error
                     * @param {jQuery} element
                     */
                    errorPlacement: function (error, element) {
                        error.insertAfter(element);
                    }
                }).on('highlight.validate', function () {
                    $(this).validation('option');
                });

                videoForm.validation();

                callback(videoForm.valid() && videoLoaded);
            }, this));

            this._blockActionButtons(false);
        },

        /**
         * Create video item icons
         */
        createVideoItemIcons: function () {
            var $imageWidget = this._gallery.find('.product-image.video-item'),
                $bannerGalleryWrapper = $(this._imageBannerGalleryWrapperSelector).find('.product-image.video-item');

            $imageWidget.parent().addClass('video-item');
            $bannerGalleryWrapper.parent().addClass('video-item');
            $imageWidget.removeClass('video-item');
            $bannerGalleryWrapper.removeClass('video-item');
            $('.video-item .action-delete').attr('title', $.mage.__('Delete video'));
            $('.video-item .action-delete span').html($.mage.__('Delete video'));
        },

        /**
         * Fired when click on create video
         * @private
         */
        _onCreate: function () {
            var nvs = this.element.find(this._videoPreviewInputSelector),
                file = nvs.get(0),
                reqClass = 'required-entry _required',
                vidvs = this.element.find('#new_video_file'),
                fileVid = vidvs.get(0),
                vidReqClass = 'required-entry _required',
                self = this;

            if (file && file.files && file.files.length) {
                file = file.files[0];
            } else {
                file = null;
            }

            if (fileVid && fileVid.files && fileVid.files.length) {
                fileVid = fileVid.files[0];
            } else {
                fileVid = null;
            }

            if (!fileVid && !this._tempPreviewVideoData) {
                vidvs.addClass(vidReqClass);
            }

            this.isValid($.proxy(
                function (videoValidStatus) {
                    if (!videoValidStatus) {
                        return;
                    }

                    if (this._tempPreviewImageData) {
                        this._onImageLoaded(this._tempPreviewImageData, null, null, $.proxy(this.close, this));
                    } else {
                        this._uploadVideo(fileVid, null, $.proxy(function () {
                            this._uploadImage(file, null, $.proxy(function () {
                                self.close();
                            }));
                        }, this));
                    }

                    nvs.removeClass(reqClass);
                    vidvs.removeClass(reqClass);
                }, this
            ));
        },

        /**
         * Fired when click on update video
         * @private
         */
        _onUpdate: function () {
            var inputFile, itemId, _inputSelector, mediaFields, imageData, flagChecked, fileName, callback;

            this.isValid($.proxy(
                function (videoValidStatus) {
                    if (!videoValidStatus) {
                        return;
                    }
                    imageData = this.imageData || {};
                    inputFile = this.element.find(this._videoPreviewInputSelector);
                    itemId = this.element.find(this._itemIdSelector).val();
                    itemId = itemId.slice(1, itemId.length - 1);

                    _inputSelector = '[name*="[' + itemId + ']"]';
                    mediaFields = this._gallery.find('input' + _inputSelector);
                    $.each(mediaFields, function (i, el) {
                        var elName = el.name,
                            start = elName.indexOf(itemId) + itemId.length + 2,
                            fieldName = elName.substring(start, el.name.length - 1),
                            _field = this.element.find('#' + fieldName),
                            _tmp;

                        if (_field.length > 0) {
                            _tmp = _inputSelector.slice(0, _inputSelector.length - 2) + '[' + fieldName + ']"]';
                            this._gallery.find(_tmp).val(_field.val());
                            imageData[fieldName] = _field.val();
                        }
                    }.bind(this));
                    flagChecked = this.element.find(this._videoDisableinputSelector).attr('checked') ? 1 : 0;
                    this._gallery.find('input[name*="' + itemId + '][disabled]"]').val(flagChecked);
                    this._gallery.find(_inputSelector).siblings('.image-fade').css(
                        'visibility', flagChecked ? 'visible' : 'hidden'
                    );
                    imageData.disabled = flagChecked;

                    if (this._tempPreviewImageData) {
                        this._onImageLoaded(
                            this._tempPreviewImageData,
                            null,
                            imageData.file,
                            $.proxy(this.close, this)
                        );
                        return;
                    }

                    fileName = inputFile.get(0).files;

                    if (!fileName || !fileName.length) {
                        fileName = null;
                    }
                    inputFile.replaceWith(inputFile);

                    callback = $.proxy(function () {
                        this.close();
                    }, this);

                    var inputVideoFile = this.element.find(this._newVideoFileSelector),
                        videoFileName = inputVideoFile.get(0).files;
                    if (!videoFileName || !videoFileName.length) {
                        videoFileName = null;
                    }
                    inputVideoFile.replaceWith(inputVideoFile);

                    if (videoFileName) {
                        this._uploadVideo(videoFileName, null, $.proxy(function (result) {
                            if (fileName) {
                                this._uploadImage(fileName, imageData.file, callback);
                            } else {
                                this._replaceImage(imageData.file, result.file, imageData);
                                callback(0, imageData);
                            }
                        }, this));
                    } else {
                        if (fileName) {
                            this._uploadImage(fileName, imageData.file, callback);
                        } else {
                            this._replaceImage(imageData.file, imageData.file, imageData);
                            callback(0, imageData);
                        }
                    }
                }, this
            ));
        },

        /**
         * Fired when clicked on cancel
         * @private
         */
        _onCancel: function () {
            this.close();
        },

        /**
         * Fired when clicked on delete
         * @private
         */
        _onDelete: function () {
            var filename = this.element.find(this._videoImageFilenameselector).val();

            this._removeImage(filename);
            this.close();
        },

        /**
         * @param {String} file
         * @param {Function} callback
         * @private
         */
        _readPreviewLocal: function (file, callback) {
            var fr = new FileReader;

            if (!window.FileReader) {
                return;
            }

            /**
             * On load end
             */
            fr.onloadend = function () {
                callback(fr.result);
            };
            fr.readAsDataURL(file);
        },

        /**
         *  Image file input handler
         * @private
         */
        _onImageInputChange: function () {
            var jFile = this.element.find(this._videoPreviewInputSelector),
                file = jFile[0],
                val = jFile.val(),
                prev = this._getPreviewImage(),
                ext = '.' + val.split('.').pop();

            if (!val) {
                return;
            }
            ext = ext ? ext.toLowerCase() : '';

            if (
                ext.length < 2 ||
                this._imageTypes.indexOf(ext.toLowerCase()) === -1 || !file.files || !file.files.length
            ) {
                prev.remove();
                this._previewImage = null;
                jFile.val('');

                return;
            } // end if
            file = file.files[0];
            this._tempPreviewImageData = null;
            this._onPreview(null, file, true);
        },

        /**
         * Change Preview
         * @param {String} error
         * @param {String} src
         * @param {Boolean} local
         * @private
         */
        _onPreview: function (error, src, local) {
            var img, renderImage;

            img = this._getPreviewImage();

            /**
             * Callback
             * @param {String} source
             */
            renderImage = function (source) {
                if (source.endsWith('mp4')) {
                    return;
                }
                if (source) {
                    img.attr({
                        'src': source
                    }).show();
                }
            };

            if (error) {
                return;
            }

            if (!local) {
                renderImage(src);
            } else {
                this._readPreviewLocal(src, renderImage);
            }
        },

        /**
         *
         * Return preview image instance
         * @returns {null}
         * @private
         */
        _getPreviewImage: function () {
            if (!this._previewImage) {
                this._previewImage = $(document.createElement('img')).css({
                    'width': '100%',
                    'display': 'none',
                    'src': ''
                });
                $(this._previewImage).insertAfter(this.element.find(this._videoPreviewImagePointer));
                $(this._previewImage).attr('data-role', 'video_preview_image');
            }
            return this._previewImage;
        },

        /**
         * Close slideout dialog
         */
        close: function () {
            this.element.modal('closeModal');
        },

        /**
         * Close dialog wrap
         * @private
         */
        _onClose: function () {
            var newVideoForm;

            this._isEditPage = true;
            this.imageData = null;

            if (this._previewImage) {
                this._previewImage.remove();
                this._previewImage = null;
            }

            if (this._previewVideo) {
                this._previewVideo.remove();
                this._previewVideo = null;
            }

            this._tempPreviewImageData = null;
            this._tempPreviewVideoData = null;
            this.element.trigger('reset');
            newVideoForm = this.element.find(this._videoFormSelector);

            $(newVideoForm).find('input[type="hidden"][name!="form_key"]').val('');
            this._gallery.find('input[name*="' + this.element.find(this._itemIdSelector).val() + '"]').parent().removeClass('active');

            try {
                newVideoForm.validation('clearError');
            } catch (e) {

            }
            newVideoForm.trigger('reset');
        },

        /**
         * Find element by fileName
         * @param {String} file
         */
        findElementId: function (file) {
            var elem = this._gallery.find('.image.item').find('input[value="' + file + '"]');

            if (!elem.length) {
                return null;
            }

            return $(elem).attr('name').replace('banner[media_gallery][images][', '').replace('][file]', '');
        },

        /**
         * Save image roles
         * @param {Object} imageData
         */
        saveImageRoles: function (imageData) {
            var data = imageData.file,
                $video,
                videoRoles,
                fileId,
                current,
                all,
                noRole = false;

            if (data && data.length > 0) {
                $video = $('.item.image.video-item');
                videoRoles = imageData.video_roles;
                fileId = $('.item.image.video-item .video_roles').data('fileId');
                current = $('.item.image.video-item input[name*="[' + fileId + '][video_roles]"]');
                all = $('.item.image.video-item input[name*="[video_roles]"]');

                $.each(all, function (i, el) {
                    var values = $(el).val(),
                        valuesAsArray = values.split(',');
                    valuesAsArray.forEach(function (value, index) {
                        var arrayIndex = videoRoles.indexOf(value);
                        if (arrayIndex != -1) {
                            delete valuesAsArray[index];
                        }
                    });
                    $(el).val(valuesAsArray.join(','));
                });
                $.each(current, function (i, el) {
                    $(el).val(videoRoles);
                });

                $video.find('[data-role=roles-labels] li:not(.item-role-no_role)').each(function (index, elem) {
                    var $elem = $(elem),
                        roleCode = $elem.data('roleCode');

                    if (videoRoles.some(function (r) {
                        return r === roleCode;
                    })) {
                        $elem.show();
                        noRole = true;
                    } else {
                        $elem.hide();
                    }
                });

                if (noRole) {
                    $video.find('[data-role=roles-labels] [data-role-code="no_role"]').hide();
                } else {
                    $video.find('[data-role=roles-labels] [data-role-code="no_role"]').show();
                }
            }
        },

        /**
         * On open dialog
         * @param {Object} e
         * @param {Object} imageData
         * @private
         */
        _onOpenDialog: function (e, imageData) {
            var formFields, flagChecked, file,
                modal = this.element.closest('.mage-new-video-dialog');

            if (imageData['media_type'] === 'external-video') {
                this.imageData = imageData;
                modal.find('.video-create-button').hide();
                modal.find('.video-delete-button').show();
                modal.find('.video-edit').show();

                formFields = modal.find(this._videoFormSelector).find('.edited-data');

                $.each(formFields, function (i, field) {
                    $(field).val(imageData[field.name]);
                });

                flagChecked = imageData.disabled > 0;
                modal.find(this._videoDisableinputSelector).prop('checked', flagChecked);

                file = modal.find('#file_name').val(imageData.file);

                $.each(modal.find('.video_image_role'), function () {
                    $(this).prop('checked', false).prop('disabled', false);
                });

                $.each(this._gallery.find('.image-placeholder').siblings('input:hidden'), function () {
                    var start, end, imageRole;
                    if ($(this).val() === file.val()) {
                        start = this.name.indexOf('[') + 1;
                        end = this.name.length - 1;
                        imageRole = this.name.substring(start, end);
                        modal.find('#new_video_form input[value="' + imageRole + '"]').prop('checked', true);
                    }
                });
            }
        },

        /**
         * Toggle buttons
         */
        toggleButtons: function () {
            var self = this,
                modal = this.element.closest('.mage-new-video-dialog');

            modal.find('.video-placeholder, .add-video-button-container > button').click(function () {
                modal.find('.video-create-button').show();
                modal.find('.video-delete-button').hide();
                modal.find('.video-edit').hide();
            });
            this._gallery.on('click', '.item.video-item', function () {
                modal.find('.video-create-button').hide();
                modal.find('.video-delete-button').show();
                modal.find('.video-edit').show();
            });
            this._gallery.on('click', '.item.video-item:not(.removed)', function () {
                var flagChecked,
                    file,
                    formFields = modal.find('.edited-data'),
                    container = $(this);

                $.each(formFields, function (i, field) {
                    container.find('input[name*="' + field.name + '"]');
                    $(field).val(container.find('input[name*="' + field.name + '"]').val());
                    if (field.name == 'video_roles[]') {
                        $(field).val(container.find('input[name*="video_roles"]').val().split(','));
                    }
                });

                flagChecked = container.find('input[name*="disabled"]').val() > 0;
                self._gallery.find(self._videoDisableinputSelector).attr('checked', flagChecked);

                file = self._gallery.find('#file_name').val(container.find('input[name*="file"]').val());

                $.each(self._gallery.find('.video_image_role'), function () {
                    $(this).prop('checked', false).prop('disabled', false);
                });

                $.each(self._gallery.find('.image-placeholder').siblings('input:hidden'), function () {
                    var start, end, imageRole;

                    if ($(this).val() !== file.val()) {
                        return null;
                    }

                    start = this.name.indexOf('[') + 1;
                    end = this.name.length - 1;
                    imageRole = this.name.substring(start, end);
                    self._gallery.find('input[value="' + imageRole + '"]').prop('checked', true);
                });
            });
        }
    });
    return $.mage.newBannerVideoDialog;
});
