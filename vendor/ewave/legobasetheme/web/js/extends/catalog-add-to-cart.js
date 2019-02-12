define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, $t) {
    'use strict';

    $.widget('mage.catalogAddToCart', $.mage.catalogAddToCart, {
        _bindSubmit: function () {
            var self = this;
            this.element.on('submit', function (e) {
                if (!$(this).valid()) {
                    return false;
                }
                e.preventDefault();
                self.submitForm($(this));
            });
        },
        /**
         * @param {String} form
         */
        ajaxSubmit: function (form) {
            var self = this;

            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',

                /** @inheritdoc */
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },

                /** @inheritdoc */
                success: function (res) {
                    var eventData, parameters;

                    self.onSuccessAjaxSubmit(res);

                    $(document).trigger('ajax:addToCart', form.data().productSku);

                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (res.backUrl) {
                        eventData = {
                            'form': form,
                            'redirectParameters': []
                        };
                        // trigger global event, so other modules will be able add parameters to redirect url
                        $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                        if (eventData.redirectParameters.length > 0) {
                            parameters = res.backUrl.split('#');
                            parameters.push(eventData.redirectParameters.join('&'));
                            res.backUrl = parameters.join('#');
                        }
                       
                        self.backUrlRedirect(res);

                        return;
                    }

                    if (res.messages) {
                        $(self.options.messagesSelector).html(res.messages);
                    }

                    if (res.minicart) {
                        $(self.options.minicartSelector).replaceWith(res.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }

                    if (res.product && res.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(res.product.statusText);
                    }
                    self.enableAddToCartButton(form);
                }
            });
        },
        backUrlRedirect: function (res) {
            window.location = res.backUrl;
        },
        setDefaultOptins: function (option, data) {
            this.options[option] = data;
        },
        onSuccessAjaxSubmit: function (res) {
            $(document).trigger('ajaxAddToCart', res);
        }
    });

    return $.mage.catalogAddToCart;
});
