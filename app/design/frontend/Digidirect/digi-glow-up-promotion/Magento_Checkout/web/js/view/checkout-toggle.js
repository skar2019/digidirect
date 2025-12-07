/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'domReady!'
], function ($, _, quote, customer) {
    'use strict';

    return {
        formatAddress : function (address) {
            if (!address){
                return '';
            }

            let parts = [];

            if (address.firstname || address.lastname) {
                parts.push((address.firstname || '') + ' ' + (address.lastname || ''));
            }

            if (address.street && Array.isArray(address.street)) {
                parts.push(address.street.join(', '));
            }

            if (address.city || address.region || address.postcode) {
                parts.push(
                    (address.city || '') +
                    (address.region ? ', ' + address.region : '') +
                    (address.postcode ? ' ' + address.postcode : '')
                );
            }

            if (address.countryId) {
                parts.push(address.countryId);
            }

            if (address.telephone) {
                parts.push('T: ' + address.telephone);
            }

            return parts.join('\n');
        },

        /**
         * Toggle down all sections
         */
        toggleDownAllSections:function () {
            $('#checkoutSteps li.glowup-checkout-li-main-steps').removeClass('active').addClass('inactive with-border');
            $('.opc-wrapper .step-content').hide();
            $('.opc-wrapper li .action-extension-toolbar').hide();
        },

        /**
         * Toggle up customer info section
         */
        toggleUpCustomerInfoSection :function () {
            //if (customer.isLoggedIn()) {
                $('#checkoutSteps li#customer-info')
                    .removeClass('inactive with-border')
                    .addClass('active')
                    .find('.step-content, .action-extension-toolbar').show();
            //}
        },

        /**
         * Toggle up shipping address section
         */
        toggleUpShippingAddressSection :function () {
            $('#checkoutSteps li#shipping')
                .removeClass('inactive with-border')
                .addClass('active')
                .find('.step-content, .action-extension-toolbar').show();
        },

        /**
         * Toggle up shipping method section
         */
        toggleUpShippingMethodSection :function () {
            $('#checkoutSteps li#opc-shipping_method')
                .removeClass('inactive with-border')
                .addClass('active')
                .find('.step-content, .action-extension-toolbar').show();
        },

        /**
         * Toggle up payment section
         */
        toggleUpPaymentMethodSection :function () {
            $('#checkoutSteps li#payment')
                .removeClass('inactive with-border')
                .addClass('active')
                .find('.step-content, .action-extension-toolbar').show();
        },

        /**
         * Show change email link, hide continue button, disable email input
         */
        showChangeEmailLink : function () {
            $("#customer-info-change-extension").removeClass('hide').show();
            $('input[name="username"]').prop('disabled', true).css('color', '#AEAEB2 !important');

            $("#checkoutSteps li#customer-info")
                .addClass('with-border')
                .find('.action-extension-toolbar').hide();
        },

        /**
         * Hide change email link, enable email input
         */
        hideChangeEmailLink : function () {
            $("#customer-info-change-extension").hide();
            $('input[name="username"]').prop('disabled', false).css('color', '#1d1d1f !important');
        },

        /**
         * Show shipping address change link section based on delivery type
         */
        showChangeShippingAddressLink : function () {
            if ($('input[name="delivery_type"]:checked').val() == 'collect') {
                this.showChangeClickCollectLink();
            } else {
                this.showChangeDeliveryLink();
            }

        },

        /**
         * Hide shipping address change link section based on delivery type
         */
        hideChangeShippingAddressLink : function () {
            if ($('input[name="delivery_type"]:checked').val() == 'collect') {
                this.hideChangeClickCollectLink();
            } else {
                this.hideChangeDeliveryLink();
            }
        },

        /**
         * Show delivery change link section
         */
        showChangeDeliveryLink : function () {
            $("#clickcollect_info_change_link_section").hide();

            $('#delivery_info_change_link_section').removeClass('hide').show();

            let formattedAddress = this.formatAddress(quote.shippingAddress());
            $("#shipping_delivery_address_content").text(formattedAddress);

            $(".field.addresses").attr("style", "display: none !important");
            $("li#shipping .action.action-show-popup").attr("style", "display: none !important");

            $('#checkout-step-shipping .collect-block').hide();

            $(".collect-type").hide();
            $('#co-shipping-form').attr("style", "display: none !important");
            $('.store-locator-wrapper').attr("style", "display: none !important");

            $("#checkoutSteps li#shipping")
                .addClass('with-border')
                .find('.action-extension-toolbar').hide();

        },

        /**
         * Hide delivery change link section
         */
        hideChangeDeliveryLink : function () {
            $("#delivery_info_change_link_section").hide();

            $(".collect-type").show();
            $('#co-shipping-form').show();

            if (customer.isLoggedIn() && $('input[name="delivery_type"]:checked').val() != 'collect') {
                $(".field.addresses").attr("style", "display: block !important");
                $("li#shipping .action.action-show-popup").attr("style", "display: block !important");
            }
        },

        /**
         * Show click & collect change link section
         */
        showChangeClickCollectLink : function () {
            $("#delivery_info_change_link_section").hide();
            $("#clickcollect_info_change_link_section").removeClass('hide').show();

            $("#checkout-step-shipping .wrap-block").attr("style", "display: none !important");

            $('#shipping_clickcollect_store_content').html($('.wrapper-title').html());

            $(".collect-type").hide();
            $('#co-shipping-form').attr("style", "display: none !important");
            $('.store-locator-wrapper').attr("style", "display: none !important");
            $('.form-shipping-address').attr("style", "display: none !important");

            $("#checkoutSteps li#shipping")
                .addClass('with-border')
                .find('.action-extension-toolbar').hide();

        },

        /**
         * Hide click & collect change link section
         */
        hideChangeClickCollectLink : function () {
            $("#clickcollect_info_change_link_section").hide();

            $(".collect-type").show();
            $('#opc-new-shipping-address').show(); // Only when customer logged in.
            $('#co-shipping-form').show();
            $('.store-locator-wrapper').attr("style", "display: block !important");
        },

        /**
         * Show shipping method change link section
         */
        showChangeShippingMethodLink : function () {
            $('#shipping-method-change-main').attr("style", "display: block !important");

            let $selectedRadio = $('input[type="radio"][name^="ko_unique_"]:checked');
            let $parentLi = $selectedRadio.closest('li');
            let carrierTitle = $parentLi.find('.col-carrier').text().trim();
            let methodTitle = $parentLi.find('.col-method').text().trim();
            let shippingPrice = $parentLi.find('.col-price .price').first().text().trim();

            $('#shipping_method_content').text(carrierTitle+' '+methodTitle+' '+shippingPrice);
            $('.form.methods-shipping').attr("style", "display: none !important");

            $("#checkoutSteps li#opc-shipping_method")
                .addClass('with-border')
                .find('.action-extension-toolbar').hide();
        },

        /**
         * Hide shipping method change link section
         */
        hideChangeShippingMethodLink : function () {
            $('#shipping-method-change-main').attr("style", "display: none !important");
            $('.form.methods-shipping').attr("style", "display: block !important");
        }
    };
});
