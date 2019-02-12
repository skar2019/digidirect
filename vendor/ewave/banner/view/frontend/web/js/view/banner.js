/* global picturefill */
define([
    'uiComponent',
    'Magento_Banner/js/model/banner',
    'underscore',
    'jquery',
    'matchMedia',
    'slickCarousel',
    'pictureFill'
], function (Component, Banner, _, $, mediaCheck) {
    'use strict';

    var timeoutDesktop,
        timeoutMobile;

    function getItems (displayMode, types, displayedBannersIds) {
        var items = [],
            banners,
            displayedBanners,
            customAttributes,
            format;
        if (!_.isEmpty(Banner.get('data')().items)) {
            banners = Banner.get('data')().items[displayMode];
            types = types ? types.split(',') : null;
            displayedBannersIds = displayedBannersIds ? displayedBannersIds.split(',') : null;
            displayedBanners = _.filter(banners, function (banner) {
                return !types ? true : _.isEmpty(_.difference(types, banner.types));
            });
            if (displayedBannersIds) {
                _.each(displayedBannersIds, function (val) {
                    var banner = _.findWhere(banners, {id: val});
                    if (!_.isEmpty(banner)) {
                        customAttributes = banner.custom_attributes;

                        switch (true) {
                            case !_.isEmpty(customAttributes.video) && !!customAttributes.images.length:
                                format = 'media';
                                break;
                            case !_.isEmpty(customAttributes.video):
                                format = 'video';
                                break;
                            case !!customAttributes.images.length:
                                format = 'image';
                                break;
                            default:
                                format = 'default';
                        }

                        if (format === 'video' || format === 'media' && window.VIDEOJS_NO_DYNAMIC_STYLE === undefined) { // if we have video banner
                            window.VIDEOJS_NO_DYNAMIC_STYLE = true; // Disabling Additional <style> Elements
                        }

                        items.push({
                            mediaQuery: customAttributes.media_query,
                            html: banner.content,
                            bannerId: banner.id,
                            format: format,
                            isLink: customAttributes.target_type,
                            linkUrl: customAttributes.target_id,
                            images: customAttributes.images,
                            video: customAttributes.video,
                            title: customAttributes.title,
                            alt: customAttributes.alt,
                            navigationTitle: customAttributes.navigation_title,
                            navigationImage: !_.isNull(customAttributes.navigation_image)
                        });
                    }
                });
            } else {
                _.each(displayedBanners, function (banner) {
                    items.push({
                        html: banner.content,
                        bannerId: banner.id
                    });
                });
            }
        }
        return items;
    }
    function getOptions (list) {
        return list;
    }

    return Component.extend({
        defaults: {
            currentVideoId: '',
            videoPlayerOptions: {}
        },
        initialize: function () {
            this._super();

            this.banner = Banner.get('data');
            this.dataArray = [];
            this.bannerSectionIdArray = [];

            _.each($('[data-banner-id]'), function (banner) {
                banner = $(banner);
                this['getItems' + banner.data('banner-id')] = getItems.bind(
                    null,
                    banner.data('display-mode'),
                    banner.data('types'),
                    banner.data('ids') + ''
                );
                this['getOptions' + banner.data('banner-id')] = getOptions.bind(
                    null,
                    banner.data('options')
                );
            }, this);
        },
        initializeItems: function (id, count, data, navigationTitles) {
            data.sectionId = id;
            data.sectionCount = count;
            data.navigationTitles = navigationTitles;

            this.dataArray.push(data); // 'dataArray' Store all banner-rotators items
            this.bannerSectionIdArray[id] = count; // 'bannerSectionIdArray' Store count of items by banner container ID
        },
        contentRendered: function (data, element) {
            var self = this,
                options,
                $bannerRotator;

            if (data.format === 'media') {
                this.updateRoleLogic(data, element);
            }

            // Find last banner item by banner container ID . Special conditions for multi-rotator on the page
            _.each(this.dataArray, function (item) {
                if (item.sectionId === data.sectionId && item.bannerId === data.bannerId) {
                    self.bannerSectionIdArray[item.sectionId] = self.bannerSectionIdArray[data.sectionId] - 1;
                    // Init slider if find last rendered banner in container
                    if (self.bannerSectionIdArray[item.sectionId] === 0) {
                        options = self['getOptions' + item.sectionId]();
                        $bannerRotator = $('[data-banner-id="' + item.sectionId + '"] [data-role="banner-rotator"]');

                        // Init picturefill
                        picturefill();

                        self.initializeSlider($bannerRotator, options, data);
                        self.initializedSlider($bannerRotator, data);
                    }
                }
            });
        },
        initializeSlider: function ($bannerRotator, options, data) {
            var self = this;

            if (data.navigationTitles) {
                options.customPaging = self.setCustomDots();
            }

            $bannerRotator.on('init', function (event, slick) {
                var $video = $(slick.$slides[slick.currentSlide]).find('video');

                $video.on('autoPlay', function (e) {
                    self.autoPlay(data.mediaQuery, $(e.currentTarget));
                });

                if ($video.data('video-popup')) {
                    $video.trigger('autoPlay');
                }
            });

            $bannerRotator.addClass('-loaded').slick(options);
        },
        initializedSlider: function ($bannerRotator, data) {
            this.controlVideo($bannerRotator, $bannerRotator.find('video'));
            this.preventPlayVideo($bannerRotator, data);
        },
        initializeVideoPlayer: function (video, callback) {
            var self = this;

            window.require(['videoPlayer'], function (videoPlayer) { // need to load video js lib
                videoPlayer(video, self.videoPlayerOptions).ready(function () {
                    if (callback !== undefined && typeof callback === 'function') {
                        callback(this);
                    }
                });
            });
        },
        initializeVideoModal: function (videoId, $video, $bannerRotator) {
            var $videoModal = $video.clone();

            $videoModal.attr('controls', 'controls');
            $videoModal.removeAttr('poster');

            // init modal
            $videoModal.modal(this.setVideoModalOptions());

            // save current video
            this.currentVideoId = videoId;
            this[videoId] = {};
            this[videoId].$video = $video;
            this[videoId].$videoModal = $videoModal;
            this[videoId].$bannerRotator = $bannerRotator;

            this.initializeVideoPlayer($videoModal.get(0), this.videoModalCallback.bind(this));
        },
        videoModalCallback: function (videoPlayer) {
            var self = this,
                videoId = self.currentVideoId,
                $video = self[videoId].$video,
                $videoModal = self[videoId].$videoModal,
                $bannerRotator = self[videoId].$bannerRotator,
                isAutoPlay = self.getSliderAutoPlayOption($bannerRotator);

            $videoModal.on('modalopened', function () {
                self.clearVideoTimeout($video);

                if (!$videoModal.data('modal-loaded')) {
                    videoPlayer.currentTime(0);
                    $videoModal.data('modal-loaded', true);
                }

                videoPlayer.play();
            });

            $videoModal.on('modalclosed', function () {
                $video.data('modal-active', false);
                $video.trigger('autoPlay');
                videoPlayer.pause();

                if (isAutoPlay) {
                    self[videoId].$bannerRotator.slick('slickPlay');
                }
            });

            $videoModal.modal('openModal');
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
        },
        renderVideo: function (element, item) {
            if (item.allow_video_popup !== '1') {
                this.initializeVideoPlayer(element, function () {
                    $(element).trigger('autoPlay');
                });
            }
        },
        playVideo: function (element) {
            var $video = $(element).closest('[data-role="banner-video"]').find('video'),
                $bannerRotator = $(element).closest('[data-role="banner-rotator"]'),
                isAutoPlay = this.getSliderAutoPlayOption($bannerRotator),
                videoNode = $video.get(0),
                videoId = $video.data('video-id');

            if ($video.data('video-popup')) {
                if (isAutoPlay) {
                    $bannerRotator.slick('slickPause');
                }

                $video.data('modal-active', true);
                videoNode.pause();

                (this[videoId] !== undefined && this[videoId].$videoModal !== undefined)
                    ? this[videoId].$videoModal.modal('openModal')
                    : this.initializeVideoModal(videoId, $video, $bannerRotator);
            } else {
                videoNode.play();
            }
        },
        setVideoModalOptions: function () {
            return {
                modalClass: '-video',
                buttons: []
            };
        },
        preventPlayVideo: function ($bannerRotator, data) {
            var self = this,
                $video;

            $bannerRotator.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
                if (currentSlide !== nextSlide) { // Prevent pseudo slide
                    $video = $(slick.$slides[currentSlide]).find('video');
                    if ($video.length) {
                        $video.get(0).pause();
                        self.clearVideoTimeout($video);
                    }

                    self.autoPlay(data.mediaQuery, $(slick.$slides[nextSlide]).find('video'));
                }
            });
        },
        autoPlay: function (mediaQuery, $video) {
            if ($video.length !== 0) {
                var videoNode = $video.get(0);
                mediaCheck({
                    media: mediaQuery[mediaQuery.length - 1],
                    entry: function () {
                        if ($video.is(':visible') && $video.data('autoplay-large')) {
                            var delay = parseFloat($video.data('autoplay-delay'));
                            if (delay) {
                                timeoutDesktop = setTimeout(function () {
                                    videoNode.play();
                                }, delay * 1000);
                            } else {
                                videoNode.play();
                            }
                        }
                    },
                    exit: function () {
                        if ($video.is(':visible') && $video.data('autoplay')) {
                            var delay = parseFloat($video.data('autoplay-delay'));
                            if (delay) {
                                timeoutMobile = setTimeout(function () {
                                    videoNode.play();
                                }, delay * 1000);
                            } else {
                                videoNode.play();
                            }
                        }
                    }
                });
            }
        },
        clearVideoTimeout: function ($video) {
            if (parseFloat($video.data('autoplay-delay'))) {
                clearTimeout(timeoutDesktop);
                clearTimeout(timeoutMobile);
            }
        },
        getSliderAutoPlayOption: function ($bannerRotator) {
            return $bannerRotator.slick('slickGetOption', 'autoplay');
        },
        controlVideo: function ($bannerRotator, $video) {
            var self = this;

            $video.on('pause ended', function (e) {
                var $target = $(e.currentTarget);

                $target.closest('[data-role="banner-video"]').removeClass('-playing');
                $target.closest('[data-banner-id]').removeClass('-playing');

                if (self.getSliderAutoPlayOption($bannerRotator) && !$target.data('modal-active')) {
                    $bannerRotator.slick('slickPlay');
                }
            });

            $video.on('play', function (e) {
                var $target = $(e.currentTarget);

                if ($target.data('video-popup')) {
                    $target.closest('[data-role="banner-video"]').addClass('-has-popup');
                }

                self.addStateClassName($target);
            });

            $video.on('playing', function (e) {
                if (self.getSliderAutoPlayOption($bannerRotator)) {
                    $bannerRotator.slick('slickPause');
                }

                self.addStateClassName($(e.currentTarget));
            });
        },
        addStateClassName: function ($video) {
            $video.closest('[data-role="banner-video"]').addClass('-playing');
            $video.closest('[data-banner-id]').addClass('-playing');
        }
    });
});
