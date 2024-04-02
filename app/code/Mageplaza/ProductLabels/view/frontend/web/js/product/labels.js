/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductLabels
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mageplaza.productlabels', {

        /**
         * @inheritDoc
         */
        _create: function () {
            this.initLabel();
        },

        initLabel: function () {
            var labelEl          = $('#design-labels-' + this.options.ruleId + '-' + this.options.productId),
                imgLabelEl       = $('#design-label-image-' + this.options.ruleId + '-' + this.options.productId),
                textLabelEl      = $('#design-label-text-' + this.options.ruleId + '-' + this.options.productId),
                labelTopPercent  = this.options.labelTopPercent,
                labelLeftPercent = this.options.labelLeftPercent,
                labelWidth       = this.options.labelWidth,
                labelHeight      = this.options.labelHeight,
                labelFontSize    = parseInt(this.options.labelFontSize, 0),
                labelFont        = this.options.labelFont,
                labelColor       = this.options.labelColor,
                galleryWidth     = parseInt(this.options.galleryWidth, 0),
                galleryHeight    = parseInt(this.options.galleryHeight, 0);

            $("[data-gallery-role=gallery-placeholder]").on('gallery:loaded', function () {
                var fotorama      = $('.fotorama-item.fotorama'),
                    productImgEl  = $('.fotorama__stage'),
                    fotoramaStage = $('.fotorama__stage__shaft'),
                    top, left, width, height;

                if ($(imgLabelEl).attr('src')) {
                    imgLabelEl.css({
                        'width': '100%',
                        'height': '100%'
                    });
                } else {
                    imgLabelEl.removeAttr('style');

                    /** fix src img label null **/
                    textLabelEl.css({
                        "-webkit-transform": "translate(0,0)"
                    });
                }

                textLabelEl.css({
                    'font-family': labelFont,
                    'color': labelColor
                });

                top    = (galleryHeight - labelHeight - 15) * labelTopPercent / 100 / galleryHeight * 100;
                left   = (galleryWidth - labelWidth) * labelLeftPercent / 100 / galleryWidth * 100;
                width  = labelWidth * 100 / galleryWidth;
                height = labelHeight * 100 / galleryHeight;

                labelEl.css({
                    'width': width + '%',
                    'height': height + '%',
                    'font-size': labelFontSize,
                    'top': top + '%',
                    'left': left + '%'
                });
                productImgEl.prepend(labelEl);
                labelEl.show();

                labelEl.after('<div id="mpfotorama"></div>');

                $('#mpfotorama').css({
                    'width': productImgEl.width(),
                    'height': productImgEl.height()
                });

                fotorama.css({
                    'overflow': 'visible'
                });

                productImgEl.css({
                    'overflow': 'visible'
                });

                if (top >= 80) {
                    labelEl.find('label.mp-tooltip').css({
                        top: '-60%'
                    });
                }

                fotoramaStage.appendTo("#mpfotorama");
            });
        }

    });

    return $.mageplaza.productlabels;
});
