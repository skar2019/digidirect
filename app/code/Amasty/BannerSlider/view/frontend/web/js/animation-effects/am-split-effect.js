/**
 *  Amasty slider split animation effect
 *
 *  @desc Split animation effect Component
 *
 *  @copyright 2009-2020 Amasty Ltd
 *  @license   https://amasty.com/license.html
 */

define([
    'jquery',
    'matchMedia'
], function ($, mediaCheck) {
    'use strict';

    var options = {
        classes: {
            splitWrapper: 'ambanner-split-wrap',
            splitBlock: 'ambanner-split-block',
            secondSlider: 'ambanner-slider-top'
        },
        selectors: {
            slider: '[data-ambanner-js="slider"]'
        },
        easeFunction: 'cubic-bezier(0.7, 0, 0.3, 1)'
    }

    /**
     * Duplicate the slick slider, make reverse slides direction for the duplicated slider
     * @param {Object} element - target jQuery element
     * @param {Object} widgetWrapper - slider wrapper. DOM element
     * @param {Number} maxItems - number of slides in the slider
     * @param {Object} widgetOptions - am.bannerSlider widget options
     * @private
     * @returns {void}
     */
    var _renderSecondSlider = function (element, widgetWrapper, maxItems, widgetOptions) {
        var sliderBottom = {},
            reverseItems = [];

        element.wrap('<div class="' + options.classes.splitBlock + '">');

        sliderBottom = element.closest('.' + options.classes.splitBlock)
            .clone()
            .addClass(options.classes.secondSlider)
            .appendTo(widgetWrapper);

        widgetWrapper.find('.' + options.classes.splitBlock)
            .wrapAll('<div class="' + options.classes.splitWrapper + '">');

        reverseItems = $(widgetOptions.selectors.sliderItem, sliderBottom).toArray().reverse();

        $(options.selectors.slider, sliderBottom).html('');

        for (var i = 0; i < maxItems; i++) {
            $(reverseItems[i]).appendTo($(options.selectors.slider, sliderBottom));
        }
    }

    /**
     * @private
     * @param {Object} widget - am.bannerSlider widget
     * @param {Object} overlayBlock - slider overlay. DOM element
     * @param {Object} element - slick slider block
     * @param {Object} target - target jQuery element
     * @param {Boolean} state - if slider hovered or not

     * @returns {void}
     */
    var _hoverCallback = function (widget, overlayBlock, element, target, state) {
        widget.hoverEffect(overlayBlock, target, state);
        widget.slickAutoplay(element, !state);
    }

    /**
     * @private
     * @param {Object} element - target jQuery element
     * @param {Object} widgetWrapper - slider wrapper. DOM element
     * @param {Number} maxItems - number of slides in the slider
     * @param {Object} widgetOptions - am.bannerSlider widget options
     * @param {Object} widget - am.bannerSlider widget
     * @returns {void}
     */
    var _addEventListeners = function (element, widgetWrapper, maxItems, widgetOptions, widget) {
        var secondSliderWrapper = widgetWrapper.find('.' + options.classes.secondSlider),
            secondSlider = $(options.selectors.slider, secondSliderWrapper),
            overlayBlock = $(widgetOptions.selectors.overlayBlock, widgetWrapper),
            splitWrapper = widgetWrapper.find('.' + options.classes.splitWrapper),
            sliderNavButtons = widgetWrapper.find(widgetOptions.selectors.sliderNavButtons),
            sliders = $(options.selectors.slider, widgetWrapper),
            arrowNextClass = widgetOptions.classes.arrowButton + ' ' + widgetOptions.classes.next,
            arrowPrevClass = widgetOptions.classes.arrowButton + ' ' + widgetOptions.classes.prev,
            dragging = false,
            tracking,
            newTracking,
            diffTracking,
            rightTracking,
            toSlide,
            initialOffsetX,
            slideDirection;

        element.on('click mousedown touchstart', function (e) {
            if ((e.target.offsetParent.className === widgetOptions.baseSliderOptions.dotsClass &&
                e.target.parentElement === e.target.offsetParent.firstChild)
                || (e.target.className === arrowPrevClass)) {
                slideDirection = 'left';

                return;
            }

            if ((e.target.offsetParent.className === widgetOptions.baseSliderOptions.dotsClass &&
                e.target.parentElement === e.target.offsetParent.lastChild)
                || (e.target.className === arrowNextClass)) {
                slideDirection = 'right';

                return;
            }

            initialOffsetX = e.offsetX;

            slideDirection = ((widgetOptions.sliderBlockWidth / 2) > e.offsetX) ? 'left' : 'right';
        }).on('mousemove touchmove', function (e) {
            if (e.target.offsetParent.className === widgetOptions.baseSliderOptions.dotsClass) {
                return;
            }

            slideDirection = (initialOffsetX < e.offsetX) ? 'left' : 'right';
        });

        element.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            toSlide = function () {
                if (maxItems === 2) {
                    switch (slideDirection) {
                        case 'left': default:
                            return nextSlide + 1;
                        case 'right':
                            return nextSlide - 1;
                    }
                } else {
                    if (currentSlide > nextSlide && nextSlide === 0 && currentSlide === maxItems - 1) {
                        return slideDirection !== 'left' ? -1 : maxItems - 1;
                    } else if (currentSlide < nextSlide && currentSlide === 0 && nextSlide === maxItems - 1) {
                        return slideDirection !== 'right' ? maxItems : 0;
                    } else {
                        return maxItems - 1 - nextSlide;
                    }
                }
            }

            widget.triggerLazyLoad(secondSlider, widget.getSlideImage($(element), nextSlide).attr('data-lazy'));
            secondSlider.slick('slickGoTo', toSlide());
        });

        element.on('mousedown touchstart', function () {
            dragging = true;
            tracking = parseInt($('.slick-track', element).css('transform').split(',')[4]);
            rightTracking = parseInt($('.slick-track', secondSlider).css('transform').split(',')[4]);
        });

        element.on('mousemove touchmove', function () {
            if (dragging) {
                newTracking = parseInt($('.slick-track', element).css('transform').split(',')[4]);
                diffTracking = newTracking - tracking;

                $('.slick-track', secondSlider)
                    .css({'transform': 'matrix(1, 0, 0, 1, ' + (rightTracking - diffTracking) + ', 0)'});
            }
        });

        element.on('mouseleave touchend mouseup', function () {
            dragging = false;
        });

        mediaCheck({
            media: widgetOptions.mediaBreakpoint,
            entry: function () {
                splitWrapper.on('mouseenter.amBanner', function () {
                    _hoverCallback(widget, overlayBlock, element, $(this), true);
                });

                splitWrapper.on('mouseleave.amBanner', function () {
                    _hoverCallback(widget, overlayBlock, element, $(this), false);
                });

                sliderNavButtons.on('focus.amBanner', function () {
                    _hoverCallback(widget, overlayBlock, element, splitWrapper, true);
                });

                sliderNavButtons.on('blur.amBanner', function () {
                    _hoverCallback(widget, overlayBlock, element, splitWrapper, false);
                });

                widget.clearHover($(options.selectors.slider, splitWrapper));
            },
            exit: function () {
                splitWrapper.off('mouseenter.amBanner mouseleave.amBanner');
                sliderNavButtons.off('focus.amBanner blur.amBanner');

                widget.showMoreEvent(secondSlider);
                _splitShowMoreEvent(element, widget, widgetOptions, secondSlider);

                sliders.on('breakpoint', function(){
                    widget.showMoreEvent(secondSlider);
                    _splitShowMoreEvent(element, widget, widgetOptions, secondSlider);
                });
            }
        });
    }

    /**
     * @private
     * @param {Object} element - target jQuery element
     * @param {Object} widget - am.bannerSlider widget
     * @param {Object} widgetOptions - am.bannerSlider widget options
     * @param {Object} slider - target slider
     * @returns {void}
     */
    var _splitShowMoreEvent = function (element, widget, widgetOptions, slider) {
        $(widgetOptions.selectors.showMoreButton, slider)
            .off('click.amBannerSplit')
            .on('click.amBannerSplit', function () {
                var currentSlide = element.find('.slick-current ' + widgetOptions.selectors.sliderItem);

                widget.hoverEffect($(widgetOptions.selectors.overlayBlock, currentSlide), false, !currentSlide.hasClass(widgetOptions.classes.active));
                widget.slickAutoplay(element, currentSlide.hasClass(widgetOptions.classes.active));
                currentSlide.toggleClass(widgetOptions.classes.active);
            });
    }

    return function (widget, element, widgetOptions) {
        var widgetWrap = element.closest(widgetOptions.selectors.sliderWrapper),
            maxItems = $(widgetOptions.selectors.sliderItem, element).length,
            selectors = options.selectors,
            classes = options.classes,
            sliderOptions = {
                topSide: {
                    cssEase: options.easeFunction,
                    infinite: true,
                },
                bottomSide: {
                    arrows: false,
                    autoplay: false,
                    infinite: true,
                    dots: false,
                    cssEase: options.easeFunction,
                    initialSlide: maxItems - 1
                }
            };

        _renderSecondSlider(element, widgetWrap, maxItems, widgetOptions);
        widget.initSlider(element, sliderOptions.topSide);
        widget.initSlider($(selectors.slider, widgetWrap.find('.' + classes.secondSlider)), sliderOptions.bottomSide);
        widget.setSliderHeight(element, 0);
        widget.setSliderHeight($(selectors.slider, widgetWrap.find('.' + classes.secondSlider)), 0, '50%');
        _addEventListeners(element, widgetWrap,  maxItems, widgetOptions, widget);
    }
});
