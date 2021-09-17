/**
 *  Amasty Click and View analytics
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('am.bannerAnalytic', {
        options: {
            elementId: null,
            url: {
                view: '',
                click: '',
            }
        },
        classes: {
            slickInitialized: 'slick-initialized',
            slickCurrent: 'slick-current'
        },
        selectors: {
            formKey: 'input[name="form_key"]',
            sliderBlock: '[data-ambanner-js="slider"]',
            slickSlide: '.slick-slide'
        },
        observerConfig: {
            attributes: true,
            attributeFilter: ['class'],
            childList: false,
            subtree: false
        },

        /**
         * @private
         * @returns {void}
         */
        _create: function () {
            this._slickInitObserver();
            this._initClickCount();
        },

        /**
         * @private
         * @returns {String}
         */
        _getFormKey: function () {
            return $(this.selectors.formKey).first().val();
        },

        /**
         * @param {Element} element - Element to observe
         * @param {Function} callback
         * @private
         * @returns {void}
         */
        _doObserve: function (element, callback) {
            var observer = new MutationObserver(callback);

            observer.observe(element, this.observerConfig);
        },

        /**
         * @private
         * @returns {void}
         */
        _slickInitObserver: function () {
            var self = this,
                slickBlock = this.element.closest(this.selectors.sliderBlock),
                mutation;

            this._doObserve(slickBlock[0], function (mutationsList, observer) {
                for (mutation of mutationsList) {
                    if (mutation.type === 'attributes' && slickBlock.hasClass(self.classes.slickInitialized)) {
                        self.element.closest(self.selectors.slickSlide).hasClass(self.classes.slickCurrent)
                            ? self._postData(self.options.url.view) : null;

                        self._initViewCount();
                        observer.disconnect();

                        return;
                    }
                }
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _initViewCount: function () {
            var self = this,
                slickSlide = this.element.closest(this.selectors.slickSlide),
                mutation;

            this._doObserve(slickSlide[0], function (mutationsList, observer) {
                for (mutation of mutationsList) {
                    if (mutation.type === 'attributes' && slickSlide.hasClass(self.classes.slickCurrent)) {
                        self._postData(self.options.url.view);
                        observer.disconnect();

                        return;
                    }
                }
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _initClickCount: function () {
            var self = this;

            this.element.on('click.amClickViewCounter', function () {
                self._postData(self.options.url.click);
            });
        },

        /**
         * @private
         * @returns {void}
         */
        _postData: function (url) {
            $.ajax({
                url: url,
                data: {
                    id: this.options.elementId,
                    form_key: this._getFormKey()
                },
                type: 'GET'
            });
        }
    });

    return $.am.bannerAnalytic;
});
