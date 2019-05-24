/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';
    return function (target) {
        $.widget('mage.sticky', target, {

            /**
             * float Block on windowScroll
             * @private
             */
            _stick: function () {
                var offset,
                    isStatic,
                    stuck,
                    maxBottomHeight,
                    container,
                    stickAfter;

                isStatic = this.element.css('position') === 'static';

                if (!isStatic && this.element.is(':visible')) {
                    container  = $(this.options.container);
                    offset = $(document).scrollTop() -
                        this.parentOffset +
                        this._getOptionValue('spacingTop');

                    offset = Math.max(0, Math.min(offset, this.maxOffset));

                    if (container.length && this.options.isFixed) {
                        maxBottomHeight = container.height() + (container.position().top / 2);
                    }

                    stuck = this.element.hasClass(this.options.stickyClass);
                    stickAfter = this._getOptionValue('stickAfter');

                    if (offset && !stuck && offset < stickAfter) {
                        offset = 0;
                    }

                    // Check bottom edge
                    if (maxBottomHeight < $(document).scrollTop()) {
                        offset = 0;
                    }

                    this.element
                        .toggleClass(this.options.stickyClass, offset > 0)
                        .css('top', offset);

                    if (this.options.isFixed) {
                        if (offset) {
                            this.element.css('width', this.parentWidth).css('top', offset);
                        } else {
                            this.element.removeAttr('style');
                        }
                    }
                }

            },

            /**
             * Defines maximum offset value of the element.
             * @private
             */
            _calculateDimens: function () {
                var $parent         = this.element.parent(),
                    topMargin       = parseInt(this.element.css('margin-top'), 10),
                    parentHeight    = $parent.height() - topMargin,
                    height          = this.element.innerHeight(),
                    maxScroll       = document.body.offsetHeight - window.innerHeight;

                if (this.options.container.length > 0) {
                    maxScroll = $(this.options.container).height();
                }

                this.parentOffset   = $parent.offset().top + topMargin;
                this.maxOffset      = maxScroll - this.parentOffset;
                this.maxScroll      = maxScroll;
                this.parentWidth    = $parent.width();

                if (this.options.isFixed) {
                    this.maxOffset = this.element.position().top / 2;
                }

                if (this.maxOffset + height >= parentHeight) {
                    this.maxOffset = parentHeight - height;
                }

                return this;
            }
        });

        return $.mage.sticky;
    }
});
