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

define(
    [
        'jquery'
    ], function ($) {
        'use strict';
        $.widget(
            'mageplaza.productLabels', {
                _create: function () {
                    var labelTemplateEl = $('#rule_label_template'),
                        labelImageEl    = $('#rule_label_image'),
                        listTemplateEl  = $('#rule_list_template'),
                        listImageEl     = $('#rule_list_image'),
                        self = this;

                    /** Enable/Disable label Image size field */
                    if (labelTemplateEl.val() === '' && labelImageEl.val() === '') {
                        self.disableImageSize();
                    } else {
                        self.enableImageSize();
                    }

                    labelImageEl.on('change', function (){
                        if ($(this).val() === '') {
                            self.disableImageSize();
                        } else {
                            self.enableImageSize();
                        }
                    })

                    labelTemplateEl.on('change', function (){
                        if ($(this).val() === '') {
                            self.disableImageSize();
                        } else {
                            self.enableImageSize();
                        }
                    })

                    /** Enable/Disable Listing Image size field */
                    if (listTemplateEl.val() === '' && listImageEl.val() === '') {
                        self.disableImageSize(true);
                    } else {
                        self.enableImageSize(true);
                    }

                    listImageEl.on('change', function (){
                        if ($(this).val() === '') {
                            self.disableImageSize(true);
                        } else {
                            self.enableImageSize(true);
                        }
                    })

                    listTemplateEl.on('change', function (){
                        if ($(this).val() === '') {
                            self.disableImageSize(true);
                        } else {
                            self.enableImageSize(true);
                        }
                    })

                    /** Label image width */
                    $('#label_image_width').on('change', function () {
                        if($(this).val() === '' || $(this).val() === 0 || $(this).val().includes("-")){
                            $(this).parent().find('.note').prepend('<label for="rule_name" generated="true" class="mage-error" id="rule_name-error">Image with must be greater than or equal to 0.</label>');
                        }else{
                            $(this).parent().find('.note .mage-error').remove();
                        }
                    });

                    /** Label image height */
                    $('#label_image_height').on('change', function () {
                        if($(this).val() === '' || $(this).val() === 0 || $(this).val().includes("-")){
                            $(this).parent().find('.note').prepend('<label for="rule_name" generated="true" class="mage-error" id="rule_name-error">Image height must be greater than or equal to 0.</label>');
                        }else{
                            $(this).parent().find('.note .mage-error').remove();
                        }
                    });

                    /** List image width */
                    $('#list_image_width').on('change', function () {
                        if($(this).val() === '' || $(this).val() === 0 || $(this).val().includes("-")){
                            $(this).parent().find('.note').prepend('<label for="rule_name" generated="true" class="mage-error" id="rule_name-error">Image with must be greater than or equal to 0.</label>');
                        }else{
                            $(this).parent().find('.note .mage-error').remove();
                        }
                    });

                    /** List image height */
                    $('#list_image_height').on('change', function () {
                        if($(this).val() === '' || $(this).val() === 0 || $(this).val().includes("-")){
                            $(this).parent().find('.note').prepend('<label for="rule_name" generated="true" class="mage-error" id="rule_name-error">Image height must be greater than or equal to 0.</label>');
                        }else{
                            $(this).parent().find('.note .mage-error').remove();
                        }
                    });
                },

                disableImageSize: function (isListing = false) {
                    var imageWidth  = $('#label_image_width'),
                        imageHeight = $('#label_image_height');
                    if (isListing) {
                        imageWidth  = $('#list_image_width'),
                        imageHeight = $('#list_image_height');
                    }
                    imageWidth.prop('disabled', true);
                    imageHeight.prop('disabled', true);
                    imageWidth.val(0);
                    imageHeight.val(0)
                },

                enableImageSize: function (isListing = false) {
                    var imageWidth  = $('#label_image_width'),
                        imageHeight = $('#label_image_height');
                    if (isListing) {
                        imageWidth  = $('#list_image_width'),
                        imageHeight = $('#list_image_height');
                    }
                    imageWidth.prop('disabled', false);
                    imageHeight.prop('disabled', false);
                }
            }
        );

        return $.mageplaza.productLabels;
    }
);
