/* global picturefill */
define([
    'underscore',
    'jquery',
    'matchMedia',
    'slickCarousel',
    'pictureFill'
], function (_, $, mediaCheck) {
    'use strict';

    $.widget('ewave.bannerStatic', {
        options: {
            item: '.banner-item',
            slick: {
                prevArrow: '<span role="button" class="slick-prev" aria-label="Previous">&larr;</span>',
                nextArrow: '<span role="button" class="slick-next" aria-label="Next">&rarr;</span>',
                pauseOnHover: false,
                pauseOnFocus: false
            }
        },
        _create: function () {
            this._prepareContent();

            // Init picturefill
            picturefill();

            this._bind();
        },
        _prepareContent: function () {
            var self = this;

            this.element.find(this.options.item).each(function (i, el) {
                if ($(this).data('format') === 'media') {
                    self.updateRoleLogic($(el).data('config'), el);
                }
            });
        },
        _bind: function () {
            this.initializeSlider(this.element, this.options.slick, this.options.config);
        },
        initializeSlider: function ($bannerRotator, options, data) {
            var self = this;

            if (data.navigationTitles) {
                options.customPaging = self.setCustomDots();
            }

            $bannerRotator.addClass('-loaded').slick(options);
        },
        updateRoleLogic: function (data, element) {
            var self = this,
                mediaQueryArray = data.mediaQuery;
            _.each(mediaQueryArray, function (query, index) {
                mediaCheck({
                    media: query,
                    entry: function () {
                        var isVideo = false,
                            isImage = false,
                            i;

                        // Banner has video for current media query
                        if (self.hasVideoRole(query, data, element, isVideo)) {
                            return false;
                        }
                        // Banner has image for current media query or has image without role ('No role')
                        if (self.hasImage(query, data, element, isImage)) {
                            return false;
                        }

                        // Find video/image if admin hasn't set role for current media query
                        // Case 1: Mobile -> Desktop (from small to large)
                        for (i = index - 1; i >= 0; i--) {
                            if (self.hasVideoRole(mediaQueryArray[i], data, element, isVideo)) {
                                return false;
                            }
                            if (self.hasImage(mediaQueryArray[i], data, element, isImage)) {
                                self.updateImageSource(element, mediaQueryArray[i]);
                                return false;
                            }
                        }
                        // Case 2: Desktop -> Mobile (from large to small)
                        for (i = index + 1; i < mediaQueryArray.length; i++) {
                            if (self.hasVideoRole(mediaQueryArray[i], data, element, isVideo)) {
                                return false;
                            }
                            if (self.hasImage(mediaQueryArray[i], data, element, isImage)) {
                                return false;
                            }
                        }
                    }
                });
            });
        },
        hasVideoRole: function (query, data, element, isVideo) {
            var $bannerItem;
            _.each(data.video[0].media_query, function (mq) {
                if (query === mq) {
                    $bannerItem = $(element).closest('[data-banner-id]');
                    $bannerItem.find('[data-role="banner-video"]').removeClass('-hide');
                    $bannerItem.find('[data-role="banner-picture"]').addClass('-hide');
                    isVideo = true;
                }
            });
            if (isVideo) {
                return true;
            }
            return false;
        },
        hasImage: function (query, data, element, isImage) {
            var $bannerItem;
            _.each(data.images, function (item) {
                if (query === item.media || _.isNull(item.media)) {
                    $bannerItem = $(element).closest('[data-banner-id]');
                    $bannerItem.find('[data-role="banner-video"]').addClass('-hide');
                    $bannerItem.find('[data-role="banner-picture"]').removeClass('-hide');
                    isImage = true;
                }
            });
            if (isImage) {
                return true;
            }
            return false;
        },
        updateImageSource: function (element, mq) {
            var $banner = $(element).closest('[data-banner-id]');
            $banner.find('.picture img').attr('src', $banner.find('.picture [media="' + mq + '"]').attr('srcset'));
        },
        setCustomDots: function () {
            return function (slider, i) {
                var $slide = $(slider.$slides[i]),
                    $navigationTitleValue = $slide.data('banner-navigation-title') || $slide.find('[data-banner-id]').data('banner-navigation-title'),
                    alt,
                    $navigationImage = $slide.data('banner-navigation-image') || $slide.find('[data-banner-id]').data('banner-navigation-image');
                if ($navigationImage) {
                    alt = 'Slide #' + (i + 1);
                    return '<img class="img" src="' + $navigationTitleValue + '" alt="' + alt + '">';
                }
                return $navigationTitleValue;
            };
        }
    });

    return $.ewave.bannerStatic;
});
