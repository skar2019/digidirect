/**
 *  Amasty slider bubble animation effect
 *
 *  @desc Bubble animation effect Component
 *
 *  @copyright 2009-2020 Amasty Ltd
 *  @license   https://amasty.com/license.html
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var options = {
        bubbleMultiplier: 2.2,
        classes: {
            bubbleModifier: '-ambanner-bubble'
        },
        nodes: {
            bubbleRight: $('<i>', {
                class: 'ambanner-bubble-element -right',
                'data-ambanner-js': 'bubble-right'
            }),
            bubbleLeft: $('<i>', {
                class: 'ambanner-bubble-element -left',
                'data-ambanner-js': 'bubble-left'
            })
        },
        selectors: {
            bubble: '[data-ambanner-js="bubble"]',
            bubbleRight: '[data-ambanner-js="bubble-right"]',
            bubbleLeft: '[data-ambanner-js="bubble-left"]',
        },
        sliderOptions: {
            fade: true,
            isBubbleEffect: true
        }
    }

    /**
     * Set width and height to the bubble element and remove them when animation is over
     * @param {Object} element - target jQuery element
     * @param {Object} widgetOptions - am.bannerSlider widget options
     * @param {String} directionElement - slider arrow selector
     * @private
     * @returns {void}
     */
    var _animateBubble = function (element, widgetOptions, directionElement) {
        var slider = element[0],
            size = (slider.clientWidth >= slider.clientHeight) ? slider.clientWidth * options.bubbleMultiplier
                : slider.clientHeight * options.bubbleMultiplier,
            bubble = element.find(directionElement),
            speed = widgetOptions.transitionSpeed / 1000;

        bubble.addClass(widgetOptions.classes.active).css({
            'width': size,
            'height': size,
            'transition': 'width ' + speed * 1.2 + 's, height ' + speed * 1.2 + 's, opacity ' + speed / 1.2 + 's ' + speed / 1.2 + 's'
        });

        bubble.on('transitionend webkitTransitionEnd', function() {
            $(this).removeClass(widgetOptions.classes.active).css({
                'width': '0',
                'height': '0',
                'transition': 'none'
            });
        });
    }

    return function (widget, element, widgetOptions) {
        element.addClass(options.classes.bubbleModifier);
        widget.initSlider(element, options.sliderOptions);

        element.prepend(options.nodes.bubbleLeft[0].outerHTML).append(options.nodes.bubbleRight[0].outerHTML);

        element.on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            if (Math.abs(nextSlide - currentSlide) === 1) {
                (nextSlide - currentSlide > 0) ? _animateBubble(element, widgetOptions, options.selectors.bubbleRight)
                    : _animateBubble(element, widgetOptions, options.selectors.bubbleLeft);
            } else {
                (nextSlide - currentSlide > 0) ? _animateBubble(element, widgetOptions, options.selectors.bubbleLeft)
                    : _animateBubble(element, widgetOptions, options.selectors.bubbleRight);
            }
        });
    }
});
