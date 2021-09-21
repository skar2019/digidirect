/**
 *  Amasty Banner Slider widget
 *
 *  @copyright 2009-2020 Amasty Ltd
 *  @license   https://amasty.com/license.html
 */

define([
    'jquery',
    'underscore',
    'matchMedia',
    'mage/translate',
    'amSliderSplitEffect',
    'amSliderBubbleEffect',
    'amBannerSliderSlick'
], function ($, _, mediaCheck, $t, amSliderSplitEffect, amSliderBubbleEffect) {
    'use strict';

    $.widget('am.bannerSlider', {
        options: {
            mediaBreakpoint: '(min-width: 768px)',
            sliderBlockWidth: null,
            sliderAnimationEffect: null,
            transitionSpeed: null,
            isAutoplay: false,
            isLazyload: false,
            sliderOptions: {},
            classes: {
                loaded: '-ambanner-loaded',
                hover: '-ambanner-hover',
                active: '-ambanner-active',
                fade: '-ambanner-fade',
                touchscreen: '-ambanner-touchscreen',
                noDots: '-ambanner-no-dots',
                arrowButton: 'ambanner-arrow-button',
                next: '-next',
                previous: '-prev',
                slickLoading: 'slick-loading',
                secondSlider: 'ambanner-slider-top',
                lazyIgnore: 'ignore-lazy-load'
            },
            selectors: {
                showMoreButton: '[data-ambanner-js="show-more"]',
                sliderItem: '[data-ambanner-js="item"]',
                sliderWrapper: '[data-ambanner-js="slider-wrapper"]',
                overlayBlock: '[data-ambanner-js="overlay"]',
                contentBlock: '[data-ambanner-js="content-block"]',
                contentInner: '[data-ambanner-js="content-inner"]',
                image: '[data-ambanner-js="image"]',
                sliderNavButtons: '.ambanner-arrow-button, .ambanner-slider-dots button',
                slickList: '.slick-list',
                slickActive: '.slick-active',
                slickClonedLazyImage: '.slick-cloned [data-lazy="%"]'
            },
            baseSliderOptions: {
                lazyLoad: 'ondemand',
                slidesToShow: 1,
                slidesToScroll: 1,
                adaptiveHeight: true,
                infinite: true,
                dotsClass: 'ambanner-slider-dots',
                nextArrow: '',
                prevArrow: ''
            },
            animationEffects: {
                rolling: 0,
                split: 1,
                bubble: 2
            }
        },
        intersectionObserverName: 'IntersectionObserver',
        loadObserver: null,
        isWidgetInit: false,

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            this.intersectionObserverName in window ? this._initLoadObserver() : this._initWidget();
        },

        /**
         * @private
         * @returns {void}
         */
        _initWidget: function () {
            this._buildArrowsNodes();

            switch (this.options.sliderAnimationEffect) {
                case this.options.animationEffects.rolling: default:
                    this.initSlider(this.element);
                    this._initListeners();
                    this.setSliderHeight(this.element, 0);
                    break;
                case this.options.animationEffects.split:
                    amSliderSplitEffect(this, this.element, this.options);
                    break;
                case this.options.animationEffects.bubble:
                    amSliderBubbleEffect(this, this.element, this.options);
                    this._initListeners();
                    this.setSliderHeight(this.element, this.options.transitionSpeed);
                    break;
            }

            this._addFadeToContent();
            this._touchDeviceCheck();
            $(window).resize(_.debounce(this._addFadeToContent.bind(this), 200));

            this.isWidgetInit = true;
        },

        /**
         * @private
         * @returns {void}
         */
        _initLoadObserver: function () {
            var self = this,
                options = {
                    root: null,
                    rootMargin: '50px',
                    threshold: 0
                };

            self.loadObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting && !self.isWidgetInit) {
                        self._initWidget();

                        !self.options.isAutoplay ? observer.unobserve(entry.target) : null;
                    }

                    if (self.options.isAutoplay && self.isWidgetInit) {
                        self.slickAutoplay(self.element, entry.isIntersecting);
                    }
                });
            }, options);

            self.loadObserver.observe(self.element[0]);
        },

        /**
         * @private
         * @returns {void}
         */
        _initListeners: function () {
            var self = this,
                overlayBlock = $(self.options.selectors.overlayBlock, self.element),
                sliderNavButtons = $(self.options.selectors.sliderNavButtons, self.element);

            mediaCheck({
                media: self.options.mediaBreakpoint,
                entry: function () {
                    self.element.on('mouseenter.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, true);
                    });

                    self.element.on('mouseleave.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, false);
                    });

                    sliderNavButtons.on('focus.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, true);
                    });

                    sliderNavButtons.on('blur.amBanner', function () {
                        self.hoverEffect(overlayBlock, self.element, false);
                    });

                    self.clearHover(self.element);
                },
                exit: function () {
                    self.showMoreEvent(self.element);

                    sliderNavButtons.off('focus.amBanner blur.amBanner');

                    self.element
                        .off('mouseenter.amBanner mouseleave.amBanner')
                        .on('breakpoint', function () {
                            self.showMoreEvent(self.element);
                        });
                }
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _addFadeToContent: function () {
            var options = this.options,
                contentBlock,
                contentInner;

            $(this.options.selectors.sliderItem).each(function (index, element) {
                contentBlock = $(options.selectors.contentBlock, element);
                contentInner = $(options.selectors.contentInner, contentBlock);

                $(element).removeClass(options.classes.fade);

                if (!contentBlock.length) {
                    return;
                }

                if (contentInner.height() > contentBlock.height()) {
                    $(element).addClass(options.classes.fade);
                }
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _touchDeviceCheck: function () {
            var sliderOptions = this.options.sliderOptions;

            if ('ontouchstart' in document.documentElement) {
                this.element
                    .addClass(this.options.classes.touchscreen)
                    .addClass(sliderOptions.arrows && !sliderOptions.dots ? this.options.classes.noDots : '');
            }
        },

        /**
         * @param {Object} target - target overlayCircle block
         * @param {Object} hoverElement - element that require a hover class
         * @param {Boolean} isHoverIn
         * @public
         * @returns {void}
         */
        hoverEffect: function (target, hoverElement, isHoverIn) {
            var sliderWidth = this.element.width(),
                sliderHeight = this.element.height();

            this.overlaySize = sliderWidth > sliderHeight ? sliderWidth * 2 : sliderHeight * 2;

            target.css({
                'width': isHoverIn ? this.overlaySize : '0',
                'height': isHoverIn ? this.overlaySize : '0'
            });

            if (hoverElement) {
                hoverElement.toggleClass(this.options.classes.hover, isHoverIn);
            }
        },

        /**
         * @param {Object} element
         * @public
         * @returns {void}
         */
        showMoreEvent: function (element) {
            var self = this,
                options = self.options;

            $(options.selectors.showMoreButton, element)
                .off('click.amBanner')
                .on('click.amBanner', function () {
                    self.hoverEffect($(this).next().find(self.options.selectors.overlayBlock), element, !$(this).hasClass(options.classes.active));

                    if (options.sliderAnimationEffect !== options.animationEffects.split) {
                        self.slickAutoplay(element, $(this).hasClass(options.classes.active));
                    }

                    $(this).toggleClass(options.classes.active)
                        .closest(options.selectors.sliderItem)
                        .toggleClass(options.classes.active);
                });
        },

        /**
         * @param {Object} element
         * @public
         * @returns {void}
         */
        clearHover: function (element) {
            element.removeClass(this.options.classes.hover)
                .find(this.options.selectors.showMoreButton)
                .removeClass(this.options.classes.active)
                .closest(this.options.selectors.sliderItem)
                .removeClass(this.options.classes.active);
        },

        /**
         * Auto play banners if state is true, stop playing if state is false
         * @param {Object} element
         * @param {Boolean} state
         * @public
         * @returns {void}
         */
        slickAutoplay: function (element, state) {
            if (this.options.isAutoplay) {
                state ? element.slick('slickPlay') : element.slick('slickPause');
            }
        },

        /**
         * @private
         * @returns {void}
         */
        _buildArrowsNodes: function () {
            var baseSliderOptions = this.options.baseSliderOptions,
                classList = this.options.classes;

            baseSliderOptions.nextArrow = '<button class="' + classList.arrowButton + ' ' + classList.next
                + '" title="' + $t('Next') + '">' + $t('Next') + '</button>';

            baseSliderOptions.prevArrow = '<button class="' + classList.arrowButton + ' ' + classList.previous
                + '" title="' + $t('Previous') + '">' + $t('Previous') + '</button>';
        },

        /**
         * @private
         * @returns {Object}
         */
        _getMergedOptions: function (restOptions) {
            return _.extend(this.options.baseSliderOptions, this.options.sliderOptions, restOptions);
        },

        /**
         * @private
         * @returns {void}
         */
        _destroySlider: function () {
            if (this.element.hasClass('slick-initialized')) {
                this.element.slick('unslick');
            }

            this.element.css('max-width', 'inherit').removeClass(this.options.classes.loaded);
            this.element.closest(this.options.selectors.sliderWrapper).css('max-width', 'inherit');
        },

        /**
         * @public
         * @returns {void}
         */
        initSlider: function (element, options) {
            element.on('init', function () {
                this.initSliderCallback(element);
            }.bind(this));

            element.css('max-width', this.options.sliderBlockWidth).slick(this._getMergedOptions(options));
            element.slick('setPosition');
            this.imageErrorHandle(element);
        },

        /**
         * @public
         * @returns {void}
         */
        initSliderCallback: function (element) {
            var image = this.getSlideImage(element, 0);

            if (!this.options.isLazyload) {
                element.addClass(this.options.classes.loaded);
            }

            if (image.hasClass(this.options.classes.lazyIgnore)) {
                image[0].style.removeProperty('height');
            }
        },

        /**
         * @param {Object} element
         * @public
         * @returns {void}
         */
        imageErrorHandle: function (element) {
            var imageSelector = this.options.selectors.image,
                isSecondSlider = element.parent().hasClass(this.options.classes.secondSlider);

            if (this.options.isLazyload) {
                element.on('lazyLoadError', function (event, slick, image) {
                    image.height(isSecondSlider ? element.height() * 2 : element.height());
                });
            } else {
                element.find(imageSelector).error(function () {
                    element.find(imageSelector + '[src="' + $(this).attr('src') + '"]')
                        .height(isSecondSlider ? element.height() * 2 : element.height());
                });
            }
        },

        /**
         * @param {Object} element
         * @param {Number} slide
         * @public
         * @returns {Object}
         */
        getSlideImage: function (element, slide) {
            return element.find('[data-slick-index="' + slide + '"]').find(this.options.selectors.image);
        },

        /**
         * @param {Object} element
         * @param {Number} delay
         * @param {Number | String} staticHeight
         * @public
         * @returns {void}
         */
        setSliderHeight: function (element, delay, staticHeight) {
            var self = this,
                selectors = this.options.selectors,
                nextSlideImageHeight,
                currentSlideImageHeight,
                heightActiveSlider,
                slideImage,
                currentAlt;

            if (!this.options.isLazyload) {
                return;
            }

            element.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
                slideImage = self.getSlideImage(element, nextSlide)[0];
                currentAlt = slideImage.alt;
                slideImage.alt = '';
                nextSlideImageHeight = self.getSlideImage(element, nextSlide).height();
                currentSlideImageHeight = self.getSlideImage(element, currentSlide).height();

                if (nextSlideImageHeight) {
                    element.animate({'height': staticHeight ? staticHeight : nextSlideImageHeight}, delay)
                        .find(selectors.slickList).animate({'min-height': nextSlideImageHeight}, delay);
                } else if (delay)  {
                    element.animate({'height': currentSlideImageHeight}, 0)
                        .find(selectors.slickList).animate({'min-height': currentSlideImageHeight}, 0);
                }

                slideImage.alt = currentAlt;
            });

            element.on('lazyLoaded', function (event, slick, image) {
                image[0].onload = function () {
                    heightActiveSlider = element.find(selectors.slickActive).height();

                    element.stop(true, true)
                        .css({'height': staticHeight ? staticHeight : heightActiveSlider})
                        .find(selectors.slickList)
                        .stop(true, true)
                        .css({'height': heightActiveSlider, 'min-height': heightActiveSlider});

                    image[0].style.removeProperty('height');
                    image.closest(element).addClass(self.options.classes.loaded);
                };
            });
        },

        /**
         * @param {Object} element
         * @param {String} lazySrc
         * @public
         * @returns {void}
         */
        triggerLazyLoad: function (element, lazySrc) {
            var img;

            if (!this.options.isLazyload) {
                return;
            }

            img = element.find(this.options.selectors.slickClonedLazyImage.replace('%', lazySrc));

            img.removeClass(this.options.classes.slickLoading).attr('src', lazySrc).removeAttr('data-lazy');
            this.processPictureSource(img.siblings('source'));
        },

        /**
         * For picture tag support
         *
         * @param {Object} pictureSources
         * @public
         * @returns {void}
         */
        processPictureSource: function (pictureSources) {
            if (pictureSources.length) {
                pictureSources.each(function (index, item) {
                    $(item).attr('srcset', $(item).data('srcset')).removeAttr('data-srcset');
                });
            }
        }
    });

    return $.am.bannerSlider;
});
