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

    $.widget('mageplaza.productlabelDesign', {
        popup: false,

        ids: {
            btnLoad: '#load-label-template',
            templateEl: '#rule_label_template',
            designLabelEl: '#design-labels',
            inputField: '#rule_label_position',
            inputLabel: '#rule_label',
            imageEl: '#design-label-image',
            inputWidth: '#label_image_width',
            inputHeight: '#label_image_height',
            labelEl: '#design-label-text',
            uploadImgEl: '#rule_label_image',
            uploadImgImg: '#rule_label_image_image',
            styleLabel: '#mpproductlabels-design-filed style',
            dfImg: '#mpproductlabels-product-img',
            gridEl: '#label-position-grid',
            inputGrid: '#rule_label_position_grid',
            fontFamily: '#rule_label_font',
            fontSize: '#rule_label_font_size',
            color: '#rule_label_color',
            customCss: '#rule_label_css',
            btnLoadList: '#load-list-template',
            templateElList: '#rule_list_template',
            designLabelElList: '#design-labels-list',
            inputFieldList: '#rule_list_position',
            inputLabelList: '#rule_list_label',
            imageElList: '#design-label-image-list',
            labelElList: '#design-label-text-list',
            inputWidthList: '#list_image_width',
            inputHeightList: '#list_image_height',
            uploadImgElList: '#rule_list_image',
            uploadImgImgList: '#rule_list_image_image',
            styleLabelList: '#mpproductlabels-design-filed-list style',
            gridElList: '#list-position-grid',
            inputGridList: '#rule_list_position_grid',
            fontFamilyList: '#rule_list_font',
            fontSizeList: '#rule_list_font_size',
            colorList: '#rule_list_color',
            customCssList: '#rule_list_css'
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this.initUploadImage();
            this.initFields();
            this.initDraggable();
            this.initPositionGrid();
            this.initImageSize();
            this.dataLabel();
            this.initListField();
            this.loadTemplate();
            this.initCustomCss();
        },

        /**
         * init grid position
         * @param type
         */
        initPositionGrid: function (type) {
            var self          = this,
                designLabelEl = $(this.ids.designLabelEl),
                imgEl         = $(this.ids.imageEl),
                dfImg         = $(this.ids.dfImg),
                gridEl        = $(this.ids.gridEl),
                inputGrid     = $(this.ids.inputGrid),
                squares       = '.squares';


            if (type === 'list') {
                designLabelEl = $(this.ids.designLabelElList);
                imgEl         = $(this.ids.imageElList);
                gridEl        = $(this.ids.gridElList);
                inputGrid     = $(this.ids.inputGridList);
                squares       = '.squares-list';
            }

            /** Set data Grid position **/
            gridEl.find('[data-pos="' + inputGrid.val() + '"]').addClass('pos-click');

            /** Hover Grid position **/
            $(squares).hover(function () {
                $(this).addClass('hover');
            }, function () {
                $(squares).removeClass('hover');
            });

            $(squares).on('click', function () {
                var position    = $(this).attr('data-pos'),
                    widthEl     = dfImg.width() - designLabelEl.width(),
                    heightEl    = dfImg.height() - designLabelEl.height(),
                    posMap      = {
                        'tl': {top: 0, left: 0},
                        'tc': {top: 0, left: widthEl / 2},
                        'tr': {top: 0, left: widthEl},
                        'cl': {top: heightEl / 2, left: 0},
                        'cc': {top: heightEl / 2, left: widthEl / 2},
                        'cr': {top: heightEl / 2, left: widthEl},
                        'bl': {top: heightEl, left: 0},
                        'bc': {top: heightEl, left: widthEl / 2},
                        'br': {top: heightEl, left: widthEl}
                    },
                    top         = posMap[position]["top"],
                    left        = posMap[position]["left"],
                    percentTop  = top / heightEl * 100,
                    percentLeft = left / widthEl * 100;

                $(squares).removeClass('pos-click');
                $(this).addClass('pos-click');
                designLabelEl.css({'top': top, 'left': left});
                inputGrid.val(position);

                self.updateFields('label', {
                    top: top,
                    left: left,
                    width: imgEl.width(),
                    height: imgEl.height(),
                    percentTop: percentTop,
                    percentLeft: percentLeft
                }, type);
            });
        },

        initCustomCss: function (type) {
            var customCss  = $(this.ids.customCss),
                styleLabel = $(this.ids.styleLabel);

            if (type === 'list') {
                customCss  = $(this.ids.customCssList);
                styleLabel = $(this.ids.styleLabelList);
            }

            styleLabel.html(customCss.val());
            customCss.on('blur', function () {
                styleLabel.html($(this).val());
            });
        },

        /**
         * init Image Size input
         * @param type
         */
        initImageSize: function (type) {
            var self          = this,
                data          = {},
                width         = 0,
                height        = 0,
                inputWidth    = $(this.ids.inputWidth),
                inputHeight   = $(this.ids.inputHeight),
                inputField    = $(this.ids.inputField),
                designLabelEl = $(this.ids.designLabelEl),
                imgEl         = $(this.ids.imageEl),
                inputGrid     = $(this.ids.inputGrid),
                squares       = $('.squares');

            if (type === 'list') {
                imgEl         = $(this.ids.imageElList);
                inputWidth    = $(this.ids.inputWidthList);
                inputHeight   = $(this.ids.inputHeightList);
                inputField    = $(this.ids.inputFieldList);
                designLabelEl = $(this.ids.designLabelElList);
                inputGrid     = $(this.ids.inputGridList);
                squares       = $('.squares-list');
            }

            /** check image null **/
            if (imgEl.attr('src') === '') {
                inputWidth.val(0);
                inputHeight.val(0);
            } else {
                data = JSON.parse(inputField.val());
                inputWidth.val(data.label.width);
                inputHeight.val(data.label.height);
            }

            inputWidth.keyup(function () {
                width = self.checkMaxSize(inputWidth, 'width', $(this).val());
                designLabelEl.css({'width': width, 'top': 0, 'left': 0});
                imgEl.css({'width': width});
                squares.removeClass('pos-click');
                inputGrid.val('');
                self.updateFields('label', {
                    top: 0,
                    left: 0,
                    width: width,
                    height: imgEl.height(),
                    percentTop: 0,
                    percentLeft: 0
                }, type);
            });

            inputHeight.keyup(function () {
                height = self.checkMaxSize(inputHeight, 'width', $(this).val());
                designLabelEl.css({'height': height, 'top': 0, 'left': 0});
                imgEl.css({'height': height});
                squares.removeClass('pos-click');
                inputGrid.val('');
                self.updateFields('label', {
                    top: 0,
                    left: 0,
                    width: imgEl.width(),
                    height: height,
                    percentTop: 0,
                    percentLeft: 0
                }, type);
            });
        },

        /**
         * data label when load page and when on change input
         * @param type
         */
        dataLabel: function (type) {
            var textLabel  = $(this.ids.labelEl),
                fontFamily = $(this.ids.fontFamily),
                size       = $(this.ids.fontSize),
                color      = $(this.ids.color);

            if (type === 'list') {
                textLabel  = $(this.ids.labelElList);
                fontFamily = $(this.ids.fontFamilyList);
                size       = $(this.ids.fontSizeList);
                color      = $(this.ids.colorList);
            }

            textLabel.css({
                'font-family': fontFamily.val(),
                'font-size': parseInt(size.val(), 0),
                'color': color.val()
            });

            $(fontFamily).on('change', function () {
                textLabel.css({'font-family': $(this).val()});
            });

            $(size).keyup(function () {
                textLabel.css({'font-size': parseInt($(this).val(), 0)});
            });

            color.on('blur', function () {
                textLabel.css({'color': $(this).val()});
            });
        },

        /**
         * init Product listing design
         */
        initListField: function () {
            var self          = this,
                isCatSameProd = $('#rule_same'),
                listEl        = $('[data-ui-id*="form-field-list"]'); //all fields under the isCatSameProd element

            if (isCatSameProd.val() === '1') {
                listEl.hide();
            } else {
                self.initUploadImage('list');
                self.initFields('list');
                self.initDraggable('list');
                self.initPositionGrid('list');
                self.initImageSize('list');
                self.dataLabel('list');
                self.loadTemplate('list');
                self.initCustomCss('list');
            }

            isCatSameProd.on('change', function () {
                if (this.value === '1') {
                    listEl.hide();
                } else {
                    listEl.show();
                    self.initUploadImage('list');
                    self.initFields('list');
                    self.initDraggable('list');
                    self.initPositionGrid('list');
                    self.initImageSize('list');
                    self.dataLabel('list');
                    self.loadTemplate('list');
                    self.initCustomCss('list');
                }
            });
        },

        /**
         * Init fields
         */
        initFields: function (type) {
            var self          = this,
                data          = {},
                inputLabel    = $(this.ids.inputLabel),
                labelEl       = $(this.ids.labelEl),
                inputField    = $(this.ids.inputField),
                designLabelEl = $(this.ids.designLabelEl),
                imageEl       = $(this.ids.imageEl);

            if (type === 'list') {
                inputLabel    = $(this.ids.inputLabelList);
                labelEl       = $(this.ids.labelElList);
                inputField    = $(this.ids.inputFieldList);
                designLabelEl = $(this.ids.designLabelElList);
                imageEl       = $(this.ids.imageElList);
            }

            inputLabel.keyup(function () {
                labelEl.text(self.replaceLabel(inputLabel.val()));

            });
            labelEl.text(self.replaceLabel(inputLabel.val()));

            if (inputField.val() === '') {
                designLabelEl.css({'top': 0, 'left': 0});
                imageEl.hide();
                self.updateFields('label', {
                    top: 0,
                    left: 0,
                    width: 0,
                    height: 0,
                    percentTop: 0,
                    percentLeft: 0
                }, type);
            } else {
                data = JSON.parse(inputField.val());
                designLabelEl.css({
                    'top': data.label.top,
                    'left': data.label.left
                });
                imageEl.show();
                imageEl.css({'width': data.label.width, 'height': data.label.height});
            }
        },

        /**
         * Ex replace variables label
         *
         * @param label
         * @returns {*}
         */
        replaceLabel: function (label) {
            return label.replace('{{discount}}', '10')
            .replace('{{discount_percent}}', '30')
            .replace('{{current_price}}', '10.00');
        },

        /**
         * Init Draggable for fields
         */
        initDraggable: function () {
            var self       = this,
                defaultImg = $('#mpproductlabels-product-img');

            $('.draggable')
            .draggable({
                snap: '#mpproductlabels-product-img',
                snapMode: "inner",
                snapTolerance: 5,
                stack: '.draggable',
                containment: '#mpproductlabels-product-img',
                stop: function (event, ui) {
                    var key         = $(this).attr('data-id'),
                        widthEl     = defaultImg.width() - $(this).width(),
                        heightEl    = defaultImg.height() - $(this).height(),
                        top         = ui.position.top,
                        imgEl       = $(self.ids.imageEl),
                        squares     = $('.squares'),
                        left        = ui.position.left,
                        percentTop  = top / heightEl * 100,
                        percentLeft = left / widthEl * 100;

                    $(self.ids.inputGrid).val('');
                    squares.removeClass('pos-click');

                    self.updateFields(key, {
                        top: top,
                        left: left,
                        width: imgEl.width(),
                        height: imgEl.height(),
                        percentTop: percentTop,
                        percentLeft: percentLeft
                    });
                }
            });

            $('.draggable-list')
            .draggable({
                snap: '#mpproductlabels-product-img-list',
                snapMode: "inner",
                snapTolerance: 5,
                stack: '.draggable-list',
                containment: '#mpproductlabels-product-img-list',
                stop: function (event, ui) {
                    var key         = $(this).attr('data-id'),
                        widthEl     = defaultImg.width() - $(this).width(),
                        heightEl    = defaultImg.height() - $(this).height(),
                        top         = ui.position.top,
                        left        = ui.position.left,
                        imgEl       = $(self.ids.imageElList),
                        squares     = $('.squares-list'),
                        percentTop  = top / heightEl * 100,
                        percentLeft = left / widthEl * 100;

                    $(self.ids.inputGridList).val('');
                    squares.removeClass('pos-click');

                    self.updateFields(key, {
                        top: top,
                        left: left,
                        width: imgEl.width(),
                        height: imgEl.height(),
                        percentTop: percentTop,
                        percentLeft: percentLeft
                    }, 'list');
                }
            });
        },

        /**
         * Init change when upload image
         */
        initUploadImage: function (type) {
            var self          = this,
                srcImg,
                data          = {},
                top           = 0,
                left          = 0,
                dfImg         = $(this.ids.dfImg),
                imageEl       = $(this.ids.imageEl),
                labelEl       = $(this.ids.labelEl),
                designLabelEl = $(this.ids.designLabelEl),
                templateEl    = $(this.ids.templateEl),
                tmpOption     = $('#rule_label_template option'),
                inputField    = $(this.ids.inputField),
                uploadImgEl   = $(this.ids.uploadImgEl),
                uploadImgImg  = $(this.ids.uploadImgImg),
                btnDeleteImg  = $('.field-label_image .delete-image'),
                inputWidth    = $(self.ids.inputWidth),
                inputHeight   = $(self.ids.inputHeight);

            if (type === 'list') {
                imageEl       = $(this.ids.imageElList);
                labelEl       = $(this.ids.labelElList);
                inputField    = $(this.ids.inputFieldList);
                designLabelEl = $(this.ids.designLabelElList);
                templateEl    = $(this.ids.templateElList);
                tmpOption     = $('#rule_list_template option');
                uploadImgEl   = $(this.ids.uploadImgElList);
                uploadImgImg  = $(this.ids.uploadImgImgList);
                btnDeleteImg  = $('.field-list_image .delete-image');
                inputWidth    = $(self.ids.inputWidthList);
                inputHeight   = $(self.ids.inputHeightList);
            }

            srcImg = uploadImgImg.attr('src');

            if (templateEl.find(":selected").val()) {
                srcImg = window.mpViewFileUrl + '/' + templateEl.find(":selected").val();
                uploadImgImg.hide();
                btnDeleteImg.hide();
            }

            imageEl.attr('src', srcImg);
            labelEl.css('position', 'unset');

            /** init remove image **/
            if (imageEl.attr('src') === '' && inputField.val()) {
                labelEl.text($('#rule_label').val());
                labelEl.css('position', 'unset');
                data = JSON.parse(inputField.val());
                top  = data.label.top;
                left = data.label.left;

                inputWidth.val(0);
                inputHeight.val(0);
                self.updateFields('label', {
                    top: top,
                    left: left,
                    width: 0,
                    height: 0,
                    percentTop: data.label.percentTop,
                    percentLeft: data.label.percentLeft
                }, type);
            }

            if (imageEl.attr('src') !== '') {
                labelEl.css('position', 'absolute');
            }

            uploadImgEl.on('change', function () {
                var reader  = new FileReader(),
                    width   = 0,
                    height  = 0,
                    dataImg = {},
                    topImg  = 0,
                    leftImg = 0;

                reader.onload = function (e) {
                    var src = e.target.result;

                    imageEl.attr('src', src);
                    tmpOption.first().attr('selected', 'selected'); //set template null

                    imageEl.on('load', function () {
                        imageEl.show();
                        uploadImgImg.show();
                        btnDeleteImg.show();
                        labelEl.css('position', 'absolute');
                        imageEl.removeAttr('style');
                        width   = imageEl.width();
                        height  = imageEl.height();
                        dataImg = JSON.parse(inputField.val());
                        topImg  = (dfImg.height() - width) * dataImg.label.percentTop / 100;
                        leftImg = (dfImg.width() - height) * dataImg.label.percentLeft / 100;

                        inputWidth.val(width);
                        inputHeight.val(height);

                        self.updateFields('label', {
                            top: topImg,
                            left: leftImg,
                            width: width,
                            height: height,
                            percentTop: dataImg.label.percentTop,
                            percentLeft: dataImg.label.percentLeft
                        }, type);

                        designLabelEl.css({'top': top, 'left': left});
                    });
                };
                if (typeof $(this)[0].files[0] !== 'undefined') {
                    reader.readAsDataURL($(this)[0].files[0]);
                }
            });
        },

        /**
         * Update value to input position hidden
         *
         * @param fieldId
         * @param param
         * @param type
         */
        updateFields: function (fieldId, param, type) {
            var self       = this,
                inputField = $(this.ids.inputField);

            if (type === 'list') {
                inputField = $(this.ids.inputFieldList);
            }

            $.each(param, function (key, value) {
                self.options.fields[fieldId][key] = value;
            });

            inputField.val(JSON.stringify(self.options.fields));
        },

        /**
         * Check Input number too large
         *
         * @param el
         * @param type
         * @param value
         * @returns {number}
         */
        checkMaxSize: function (el, type, value) {
            var maxWidth  = $(this.ids.dfImg).width(),
                maxHeight = $(this.ids.dfImg).height();

            if (value < 0 || !value) {
                return 0;
            }

            if (type === 'width') {
                return value <= maxWidth ? value : maxWidth;
            }

            return value <= maxHeight ? value : maxHeight;
        },

        /**
         * Load template
         */
        loadTemplate: function (type) {
            var self          = this,
                btnLoad       = $(this.ids.btnLoad),
                templateEl    = $(this.ids.templateEl),
                imageEl       = $(this.ids.imageEl),
                designLabelEl = $(this.ids.designLabelEl),
                uploadImgImg  = $(this.ids.uploadImgImg),
                btnDeleteImg  = $('.field-label_image .delete-image'),
                dfImg         = $(this.ids.dfImg),
                inputWidth    = $(this.ids.inputWidth),
                inputHeight   = $(this.ids.inputHeight),
                gridEl        = $(this.ids.gridEl),
                inputGrid     = $(this.ids.inputGrid),
                squares       = $('.squares');

            if (type === 'list') {
                btnLoad       = $(this.ids.btnLoadList);
                templateEl    = $(this.ids.templateElList);
                imageEl       = $(this.ids.imageElList);
                designLabelEl = $(this.ids.designLabelElList);
                uploadImgImg  = $(this.ids.uploadImgImgList);
                btnDeleteImg  = $('.field-list_image .delete-image');
                inputWidth    = $(this.ids.inputWidthList);
                inputHeight   = $(this.ids.inputHeightList);
                gridEl        = $(this.ids.gridElList);
                inputGrid     = $(this.ids.inputGridList);
                squares       = $('.squares-list');
            }

            btnLoad.on('click', function () {
                var position,
                    src             = templateEl.find(":selected").val(),
                    index           = templateEl.find(":selected").index(),
                    top             = 0,
                    left            = 0,
                    width           = 0,
                    height          = 0,
                    widthEl         = 0,
                    heightEl        = 0,
                    checkTopRight   = false,
                    checkBottomLeft = false;

                imageEl.attr('src', window.mpViewFileUrl + '/' + src);
                uploadImgImg.hide();
                btnDeleteImg.hide();

                /** set position on grid position **/
                squares.removeClass('pos-click');
                position = gridEl.find('[data-pos="tl"]');
                position.addClass('pos-click');
                inputGrid.val('tl');

                if (self.checkPositionTopRight(index)) {
                    checkTopRight = true;
                    squares.removeClass('pos-click');
                    position = gridEl.find('[data-pos="tr"]');
                    position.addClass('pos-click');
                    inputGrid.val('tr');
                } else if (self.checkPositionBottomLeft(index)) {
                    checkBottomLeft = true;
                    squares.removeClass('pos-click');
                    position = gridEl.find('[data-pos="bl"]');
                    position.addClass('pos-click');
                    inputGrid.val('bl');
                }

                imageEl.on('load', function () {
                    imageEl.removeAttr('style');
                    width    = imageEl.width();
                    height   = imageEl.height();
                    widthEl  = dfImg.width() - width;
                    heightEl = dfImg.height() - height;

                    if (checkTopRight) {
                        left = widthEl;
                    } else if (checkBottomLeft) {
                        top = heightEl;
                    }

                    inputWidth.val(width);
                    inputHeight.val(height);

                    self.updateFields('label', {
                        top: top,
                        left: left,
                        width: width,
                        height: height,
                        percentTop: top / heightEl * 100,
                        percentLeft: left / widthEl * 100
                    }, type);

                    designLabelEl.css({'top': top, 'left': left});
                });

                self.setDataTemplate(index, type);
                self.setTextColor(index, type);
            });
        },

        /**
         * check position = top right of template
         *
         * @param index
         * @returns {boolean}
         */
        checkPositionTopRight: function (index) {
            var topRightTemplate = [1, 4, 6, 7, 9, 11, 13, 17, 19, 24, 27, 38, 40, 49];

            return $.inArray(index, topRightTemplate) > -1;
        },

        /**
         * check position = bottom left of template
         *
         * @param index
         * @returns {boolean}
         */
        checkPositionBottomLeft: function (index) {
            var bottomLeftTemplate = [8, 23, 28, 37];

            return $.inArray(index, bottomLeftTemplate) > -1;
        },

        /**
         * set Data template
         *
         * @param index
         * @param type
         */
        setDataTemplate: function (index, type) {
            var self          = this,
                labelEl       = $(this.ids.labelEl),
                designLabelEl = $(this.ids.designLabelEl),
                inputLabel    = $(this.ids.inputLabel),
                customCss     = $(this.ids.customCss),
                inputWidth    = $(this.ids.inputWidth),
                inputHeight   = $(this.ids.inputHeight),
                imgEl         = $(this.ids.imageEl),
                squares       = '.squares';

            if (type === 'list') {
                labelEl       = $(this.ids.labelElList);
                inputLabel    = $(this.ids.inputLabelList);
                designLabelEl = $(this.ids.designLabelElList);
                customCss     = $(this.ids.customCssList);
                inputWidth    = $(this.ids.inputWidthList);
                inputHeight   = $(this.ids.inputHeightList);
                imgEl         = $(this.ids.imageElList);
                squares       = '.squares-list';
            }

            switch (index){
                case 0:
                    imgEl.hide();
                    inputLabel.val('');
                    labelEl.text('');
                    labelEl.css({'position': 'unset'});
                    inputWidth.val(0);
                    inputHeight.val(0);
                    self.styleTemplate(0, 0, 50, 50, 0, type, 0);
                    customCss.val('');
                    designLabelEl.css({'top': 0, 'left': 0});
                    self.updateFields('label', {
                        top: 0,
                        left: 0,
                        width: 0,
                        height: 0,
                        percentTop: 0,
                        percentLeft: 0
                    }, type);
                    $(squares).removeClass('pos-click');
                    break;
                case 11 :
                case 24 :
                    self.styleTemplate(-8, 8, 36, 64, 42, type);
                    break;
                case 12 :
                case 25 :
                    self.styleTemplate(-8, -8, 38, 35, -41, type);
                    break;
                case 1 :
                case 13 :
                    self.styleTemplate(-8, 8, 32, 66, 42, type);
                    break;
                case 2 :
                case 10 :
                    self.styleTemplate(-8, -8, 33, 34, -41.5, type);
                    break;
                case 14 :
                case 15 :
                case 16 :
                case 20 :
                case 21 :
                case 26 :
                case 3 :
                case 29 :
                case 30 :
                    self.styleTemplate(0, -8, 48, 53, 0, type);
                    break;
                case 17 :
                case 4 :
                    self.styleTemplate(0, 8, 48, 50, 0, type);
                    break;
                case 18 :
                case 5 :
                    self.styleTemplate(-8, -8, 43, 46, -44.5, type);
                    break;
                case 19 :
                case 6 :
                    self.styleTemplate(-8, 8, 45, 58, 45, type);
                    break;
                case 22 :
                case 27 :
                case 52 :
                case 53 :
                case 54 :
                    self.styleTemplate(-8, 0, 50, 50, 0, type);
                    break;
                case 23 :
                case 28 :
                    self.styleTemplate(8, 0, 50, 50, 0, type);
                    break;
                case 31 :
                case 41 :
                    self.styleTemplate(0, -12, 45, 50, 0, type);
                    break;
                case 40 :
                case 49 :
                    self.styleTemplate(0, 5, 47, 50, 0, type);
                    break;
                case 32 :
                case 42 :
                    self.styleTemplate(0, -12, 47, 50, 0, type);
                    break;
                case 35 :
                    self.styleTemplate(0, 0, 48, 50, 0, type);
                    break;
                case 36 :
                    self.styleTemplate(0, 0, 45, 47, 0, type);
                    break;
                case 34 :
                    self.styleTemplate(0, 0, 35, 54, 19, type);
                    break;
                case 50 :
                    self.styleTemplate(0, -8, 42, 44, 0, type);
                    break;
                case 51 :
                    self.styleTemplate(0, -8, 50, 50, 0, type);
                    break;
                case 55 :
                    self.styleTemplate(-8, 0, 44, 50, 0, type);
                    break;
                case 8 :
                    self.styleTemplate(-10, 0, 50, 50, 0, type);
                    break;
                case 37 :
                    self.styleTemplate(-10, 0, 50, 50, 0, type);
                    break;
                default:
                    self.styleTemplate(0, 0, 50, 50, 0, type);
                    break;
            }
        },

        setTextColor: function (index, type) {
            var self       = this,
                colorInput = $(this.ids.color);

            if (type === 'list') {
                colorInput = $(this.ids.colorList);
            }

            switch (index){
                case 0:
                    colorInput.val('#000000');
                    colorInput.css({'background-color': '#000000', 'color': '#FFFFFF'});
                    break;
                case 9 :
                case 11 :
                case 14 :
                case 20 :
                case 31 :
                case 34 :
                case 39 :
                case 40 :
                case 50 :
                case 51 :
                case 52 :
                case 53 :
                case 54 :
                case 55 :
                    self.textColor('#FFFAF0', type);
                    break;
                case 12 :
                case 13 :
                case 15 :
                case 17 :
                case 18 :
                case 19 :
                case 23 :
                case 36 :
                    self.textColor('#F5F5DC', type);
                    break;
                case 10 :
                    self.textColor('#F5F5DC', type);
                    break;
                case 16 :
                case 22 :
                    self.textColor('#EE2C2C', type);
                    break;
                case 33 :
                    self.textColor('#C60000', type);
                    break;
                case 35 :
                    self.textColor('#FF0000', type);
                    break;
                case 37 :
                    self.textColor('#E8152C', type);
                    break;
                default:
                    self.textColor('#FFFFFF', type);
                    break;
            }
        },

        textColor: function (color, type) {
            var colorInput = $(this.ids.color);

            if (type === 'list') {
                colorInput = $(this.ids.colorList);
            }

            colorInput.val(color);
            colorInput.css({'background-color': color, 'color': '#000000'});
        },

        styleTemplate: function (labelTop, labelLeft, textTop, textLeft, rotate, type, index) {
            var textStyle,
                labelStyle,
                designLabelEl = $(this.ids.designLabelEl),
                labelEl       = $(this.ids.labelEl),
                imgEl         = $(this.ids.imageEl),
                customCss     = $(this.ids.customCss),
                styleLabel    = $(this.ids.styleLabel),
                designLabel   = this.ids.designLabelEl,
                label         = this.ids.labelEl;

            if (type === 'list') {
                designLabelEl = $(this.ids.designLabelElList);
                labelEl       = $(this.ids.labelElList);
                imgEl         = $(this.ids.imageElList);
                customCss     = $(this.ids.customCssList);
                styleLabel    = $(this.ids.styleLabelList);
                designLabel   = this.ids.designLabelElList;
                label         = this.ids.labelElList;
            }

            if (index !== 0) {
                imgEl.show();
                labelEl.css('position', 'absolute');
            }
            labelEl.css({
                'top': textTop + '%',
                'left': textLeft + '%',
                'transform': 'translateX(-50%) translateY(-50%) rotate(' + rotate + 'deg)'
            });
            designLabelEl.css({
                'margin-top': labelTop + 'px',
                'margin-l eft': labelLeft + 'px'
            });

            textStyle  = label + ' {top:' + textTop + '%; left:' + textLeft +
                '%; transform: translateX(-50%) translateY(-50%) rotate(' + rotate + 'deg)}';
            labelStyle = designLabel + '{margin-top:' + labelTop + 'px; margin-left:' + labelLeft + 'px}';

            customCss.val(textStyle + ' ' + labelStyle);
            styleLabel.text(customCss.val());
        }
    });

    return $.mageplaza.productlabelDesign;
});

