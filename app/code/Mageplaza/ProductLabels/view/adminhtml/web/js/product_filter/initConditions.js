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
    "use strict";

    $.widget('mageplaza.initConditions', {
        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.initPreviewProduct();
        },

        initPreviewProduct: function () {
            var data,
                conditionUrl = this.options.url;

            $('.mpproductlabels-list-button').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                data = $("form#edit_form").serialize();
                $.ajax({
                    type: "POST",
                    url: conditionUrl,
                    data: data,
                    cache: true,
                    showLoader: true,
                    success: function (res) {
                        $('.mpproductlabels-list')
                        .html(res)
                        .trigger('contentUpdated')
                        ;
                    }
                });
            });
        }
    });

    return $.mageplaza.initConditions;
});
