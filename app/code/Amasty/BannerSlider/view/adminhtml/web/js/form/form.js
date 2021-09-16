define([
    'jquery',
    'Magento_Ui/js/form/form'
], function ($, Form) {
    'use strict';

    return Form.extend({
        reset: function () {
            this.source.trigger('data.reset');
            $('[data-bind*=datepicker]').val('');
            var iframe = jQuery("#amasty_bannerslider_banner_form_hover_text_ifr"),
                value = jQuery("#amasty_bannerslider_banner_form_hover_text").val();
            iframe.contents().find("#tinymce").html(value);
        }
    });
});
