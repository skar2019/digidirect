define([
    'jquery',
    'slickCarousel',
    'mage/validation'
], function ($, slick) {
    'use strict';

    $.widget('ewave.gitCardImages', {
        options: {
            giftSlider: '[data-role=gift-slider]',
            mainImageGallery: '[data-gallery-role=gallery]',
            eventElement: '[data-role=gift-image]',
            productForm: '#product_addtocart_form',
            inputGift: '[name="ewave_giftcard_image"]',
            clearGallery: true,
            dataImages: [],
            enableSlider: true,
            selectedImadeId: 0,
            slickSettings: {}
        },

        _create: function () {
            this.isGiftImageAdded = false;
            this.imagesData = {};
            this.singleMode = false;
            this.slider = this.element.find(this.options.giftSlider);

            if (this.options.selectedImadeId) {
                this._setSelectedImage();
            }

            if (this.options.enableSlider) {
                this._initSlider();
            } else {
                this._removeLoader();
            }
            this._convertDataImages();
            this._bind();
        },
        _bind: function () {
            var self = this;

            this.element.on('click', this.options.eventElement, function (e) {
                var item = $(e.target)[0].tagName.toLowerCase() === 'img' ? $(e.target).parent() : $(e.target),
                    imgId = item.data('gift-image-id');
                if (!item.hasClass('-active') && imgId) {
                    $(self.options.eventElement).removeClass('-active');
                    item.addClass('-active');
                    self._setImageData(imgId);
                }
                e.preventDefault();
            });

            $(this.options.productForm).on('submit', $.proxy(self._checkValidateInput, self));
        },
        _initSlider: function () {
            var self = this;
            this.slider.on('init', $.proxy(self._removeLoader, self));
            this.slider.slick(this.options.slickSettings);
            this.slider.removeClass('-inactive').addClass('-active');
        },
        _setImageData: function (imgId) {
            if (this.fotoramaApi) {
                var imgData = this.imagesData[imgId];
                if (this.singleMode) {
                    this.fotoramaApi.load([imgData]);
                } else {
                    if (this.isGiftImageAdded && this.options.clearGallery) {
                        this.fotoramaApi.pop();
                    }
                    if (this.options.clearGallery || !imgData.i) {
                        this.fotoramaApi.push(this._clearImgData(imgData));
                    }
                    this.isGiftImageAdded = true;
                    this.fotoramaApi.show(!this.options.clearGallery && imgData.i ? imgData.i - 1 : this.fotoramaApi.size - 1);
                }
                $(this.options.inputGift).val(imgId);
            } else {
                this._getFotoramaApi(imgId);
            }
        },
        _getFotoramaApi: function (imgId) {
            this.fotoramaApi = $(this.options.mainImageGallery).data('fotorama');
            this.singleMode = !(this.fotoramaApi.size > 1);
            this._setImageData(imgId);
        },
        _convertDataImages: function () {
            var self = this;
            self.options.dataImages.forEach(function (img) {
                self.imagesData[img.imgId] = img;
            });
        },

        // Clear from properties fotorama
        _clearImgData: function (imgData) {
            imgData.i = undefined;
            imgData.$navThumbFrame = undefined;
            imgData.$stageFrame = undefined;
            return imgData;
        },

        _checkValidateInput: function () {
            if ($(this.options.inputGift).valid()) {
                this.slider.removeClass('-error');
            } else {
                this.slider.addClass('-error');
            }
        },

        _removeLoader: function () {
            this.element.find('[data-role=loader]').remove();
            this.element.removeClass('-loading');
        },

        _setSelectedImage: function () {
            var selectedItem = $(this.options.eventElement).filter('[data-gift-image-id=' + this.options.selectedImadeId + ']'),
                countItems, visibleItems, indexSelected;

            selectedItem.addClass('-active');

            if (this.options.enableSlider) {
                countItems = $(this.options.eventElement).length;
                visibleItems = this.options.slickSettings.slidesToShow;
                indexSelected = $(this.options.eventElement).index(selectedItem);
                if (countItems > visibleItems) {
                    this.options.slickSettings.initialSlide = countItems - indexSelected >= visibleItems ? indexSelected : countItems - visibleItems;
                }
            }
        }

    });

    return $.ewave.gitCardImages;
});
