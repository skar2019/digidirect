define([
    'jquery',
    'underscore',
    'jquery/ui'
], function ($, _) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _ExtendProductMedia: function (response, images) {
                _.extend(images[0], {
                    alternative: response.alternative_image
                });
                return images;
            },
            /**
             * Update [gallery-placeholder] or [product-image-photo]
             * @param {Array} images
             * @param {jQuery} context
             * @param {Boolean} isInProductView
             */
            updateBaseImage: function (images, context, isInProductView) {
                var justAnImage = images[0],
                    initialImages = this.options.mediaGalleryInitial,
                    gallery = context.find(this.options.mediaGallerySelector).data('gallery'),
                    imagesToUpdate,
                    isInitial,
                    $multiView,
                    $multiViewImage,
                    $defaultPhoto;

                if (isInProductView) {
                    imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                    isInitial = _.isEqual(imagesToUpdate, initialImages);

                    if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                        imagesToUpdate = imagesToUpdate.concat(initialImages);
                    }

                    imagesToUpdate = this._setImageIndex(imagesToUpdate);
                    gallery.updateData(imagesToUpdate);

                    if (isInitial) {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                    } else {
                        $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                            selectedOption: this.getProduct(),
                            dataMergeStrategy: this.options.gallerySwitchStrategy
                        });
                    }

                    gallery.first();
                } else if (justAnImage && justAnImage.img) {
                    $multiView = context.find('.multiview-container');
                    $multiViewImage = $multiView.find('.image');
                    $defaultPhoto = context.find('.product-image-photo');
                    $defaultPhoto.attr('src', justAnImage.img);

                    if (!justAnImage.alternative) {
                        $multiView.addClass('-no-multiview');
                    } else {
                        $multiView.removeClass('-no-multiview');
                        if ($multiViewImage.length) {
                            $multiViewImage.attr('src', justAnImage.alternative);
                        } else {
                            $('<img>', {
                                src: justAnImage.alternative,
                                class: 'image',
                                width: $defaultPhoto.attr('width'),
                                height: $defaultPhoto.attr('height')
                            }).insertAfter($defaultPhoto);
                        }
                    }
                }
            }
        });

        return $.mage.SwatchRenderer;
    };
});
