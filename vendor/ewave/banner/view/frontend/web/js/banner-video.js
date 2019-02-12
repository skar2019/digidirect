define([
    'underscore',
    'jquery',
    'matchMedia',
    'videoPlayer',
    'slickCarousel',
    'bannerStatic'
], function (_, $, mediaCheck, videoPlayer) {
    'use strict';

    var timeoutDesktop,
        timeoutMobile;

    $.widget('ewave.bannerVideo', {
        options: {
            videoPlayerOptions: {},
            playButton: '.play-button'
        },
        _create: function () {
            var self = this;
            this.disableDynamicStyles();
            this.element.find('video').each(function (i, el) {
                self.renderVideo(el, $(el).data('config'));
            });

            this._bind();
        },
        disableDynamicStyles: function () {
            if (window.VIDEOJS_NO_DYNAMIC_STYLE === undefined) {
                window.VIDEOJS_NO_DYNAMIC_STYLE = true; // Disabling Additional <style> Elements
            }
        },
        renderVideo: function (element, item) {
            if (item.allow_video_popup !== '1') {
                this.initializeVideoPlayer(element, function () {
                    $(element).trigger('autoPlay');
                });
            }
        },
        _bind: function () {
            var slick = this.element.slick('getSlick'),
                $video = $(slick.$slides[slick.currentSlide]).find('video');

            $video.on('autoPlay', $.proxy(function (e) {
                this.autoPlay(this.options.mediaQuery, $(e.currentTarget));
            }, this));

            if ($video.data('video-popup')) {
                $video.trigger('autoPlay');
            }

            this.initializedSlider();

            this.element.find(this.options.playButton).on('click', $.proxy(function (e) {
                this.playVideo(e.currentTarget);
            }, this));
        },
        initializedSlider: function () {
            this.controlVideo(this.element, this.element.find('video'));
            this.preventPlayVideo(this.element, this.options);
        },
        initializeVideoPlayer: function (video, callback) {
            videoPlayer(video, this.options.videoPlayerOptions).ready(function () {
                if (callback !== undefined && typeof callback === 'function') {
                    callback(this);
                }
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

    return $.ewave.bannerVideo;
});
