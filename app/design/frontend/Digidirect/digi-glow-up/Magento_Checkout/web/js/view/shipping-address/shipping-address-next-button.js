define([
    'jquery',
    'domReady!',
    'mage/translate',
    'Magento_Checkout/js/view/shipping',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/shipping-save-processor',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/quote',
    'uiRegistry',
    'mage/validation',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/address-converter'
], function (
    $,
    domReady,
    $t,
    ShippingComponent,
    stepNavigator,
    shippingSaveProcessor,
    shippingService,
    quote,
    registry,
    validation,
    customer,
    addressConverter
) {
    'use strict';

    function toggleShippingMethod() {
        $('#checkoutSteps li').removeClass('active').addClass('inactive');
        $('.opc-wrapper .step-content').hide();
        $('.opc-wrapper li .action-extension-toolbar').hide();

        if (!customer.isLoggedIn()) {
            $('#checkoutSteps li#customer-info').removeClass('inactive').addClass('active');
            $('#checkoutSteps li#customer-info .step-content').show();
            $('#checkoutSteps li#customer-info').css('border', 'none');
            $('#checkoutSteps  li#customer-info .action-extension-toolbar').show();
        }

        $('#checkoutSteps li#shipping').removeClass('inactive').addClass('active');
        $('#checkoutSteps li#shipping .step-content').show();
        $('#checkoutSteps li#shipping').css('border', 'none');
        $('#checkoutSteps  li#shipping .action-extension-toolbar').show();

        if ($('input[name="delivery_type"]:checked').val() == 'delivery') {
            $('#checkoutSteps li#opc-shipping_method').removeClass('inactive').addClass('active');
            $('#checkoutSteps li#opc-shipping_method .step-content').show();
            $('#checkoutSteps li#opc-shipping_method').css('border', 'none');
            $('#checkoutSteps  li#opc-shipping_method .action-extension-toolbar').show();

            var target = $('#checkoutSteps li#opc-shipping_method');

            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 600);
            }

            $('#checkoutSteps  .shipping-methods li').removeClass('inactive').addClass('active');

            $('#checkout-step-shipping .collect-block').hide();

            $('#s_method_standard_standard').prop('checked', false);
            $('#s_method_express_express').prop('checked', false);

            $('input[type="radio"][name^="ko_unique_"]').removeAttr('disabled');

            $('#checkoutSteps li#payment').removeClass('active').addClass('inactive');
            $('#checkoutSteps li#payment .step-content').hide();
            $('#checkoutSteps  li#payment .action-extension-toolbar').hide();

        } else {
            if (customer.isLoggedIn()) {
                //$(".field.addresses").attr("style", "display: block !important");
                //$("li#shipping .action.action-show-popup").attr("style", "display: block !important");
                $('#payment .step-title.accordion-step').text('2. Payment');
            } else {
                $('#payment .step-title.accordion-step').text('3. Payment');
            }

            $('#checkoutSteps li#payment').removeClass('inactive').addClass('active');
            $('#checkoutSteps li#payment .step-content').show();
            $('#checkoutSteps li#payment').css('border', 'none');
            $('#checkoutSteps  li#payment .action-extension-toolbar').show();

            var target = $('#checkoutSteps li#payment');

            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 600);
            }

            $('#checkoutSteps li#payment #latipay-form li.latipay-options-item').removeClass('inactive').addClass('active');
        }

    }

    function formatAddress(address) {
        if (!address) {
            return '';
        }

        var parts = [];

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
    }
    function changeDeliveryLinkShow() {
        $(".collect-type").hide();
        $('#co-shipping-form').attr("style", "display: none !important");
        $("#clickcollect_info_change_link_section").hide();
        $('.store-locator-wrapper').attr("style", "display: none !important");

        $('#delivery_info_change_link_section').attr("style", "display: block !important");
        let formattedAddress = formatAddress(quote.shippingAddress());
        $("#shipping_delivery_address_content").text(formattedAddress);

        $(".field.addresses").attr("style", "display: none !important");
        $("li#shipping .action.action-show-popup").attr("style", "display: none !important");

        $('#delivery-info-button-extension').hide();
    }

    function changeClickCollectLinkShow() {
        $(".collect-type").hide();
        $('#co-shipping-form').attr("style", "display: none !important");
        $("#delivery_info_change_link_section").hide();
        $("#checkout-step-shipping .wrap-block").attr("style", "display: none !important");
        $('.store-locator-wrapper').attr("style", "display: none !important");

        $("#clickcollect_info_change_link_section").attr("style", "display: block !important");
        $('#shipping_clickcollect_store_content').html($('.wrapper-title').html());

        $('#delivery-info-button-extension').hide();
    }

    $(document).on("click", "#clickcollect-info-change-extension, #clickcollect-store-change-extension", function () {
        $("#clickcollect_info_change_link_section").hide();
        $(".collect-type").show();
        $('#co-shipping-form').show();
        //$("#checkout-step-shipping .wrap-block").attr("style", "display: block !important");
        $('.store-locator-wrapper').attr("style", "display: block !important");

        $('#delivery-info-button-extension').show();
    });

    $(document).on("click", "#delivery-info-change-extension, #delivery-address-change-extension", function () {
        $("#delivery_info_change_link_section").hide();
        $(".collect-type").show();
        $('#co-shipping-form').show();

        if (customer.isLoggedIn() && $('input[name="delivery_type"]:checked').val() != 'collect') {
            $(".field.addresses").attr("style", "display: block !important");
            $("li#shipping .action.action-show-popup").attr("style", "display: block !important");
        }

        $('#delivery-info-button-extension').show();
    });

    $(document).on("click",'input[name="delivery_type"]', function () {
        if ($(this).val() == 'collect') {
            //$("#checkout-step-shipping .wrap-block").attr("style", "display: block !important");
            $('.store-locator-wrapper').attr("style", "display: block !important");
            if (!$('a.link.-collect.action.primary').length) {
                $('input[name="storeSelection"]').prop('checked', false);
            }

            $(".field.addresses").attr("style", "display: none !important");
            $("li#shipping .action.action-show-popup").attr("style", "display: none !important");

        } else {
            if (customer.isLoggedIn()) {
                $(".field.addresses").attr("style", "display: block !important");
                $("li#shipping .action.action-show-popup").attr("style", "display: block !important");
            }

            $("#checkout-step-shipping .wrap-block").attr("style", "display: none !important");
            $('.store-locator-wrapper').attr("style", "display: none !important");
        }
    });

    $(document).on("click", "#delivery-info-button-extension", function () {

        if ($('input[name="delivery_type"]:checked').val() == 'collect' && !$('a.link.-collect.action.primary').length) {
            $('input[name="storeSelection"]').prop('checked', false);
            return ;
        }

        if ($('input[name="delivery_type"]:checked').val() == 'collect') {
            var dummyAddress = {
                firstname: 'Store',
                lastname: 'Pickup',
                street: ['Click & Collect'],
                city: 'N/A',
                region: 'N/A',
                regionId: 0,
                regionCode: null,
                countryId: 'AU',
                postcode: '0000',
                telephone: '0000000000',
                save_in_address_book: 0
            };

            var shippingAddress = addressConverter.formAddressDataToQuoteAddress(dummyAddress);
            quote.shippingAddress(shippingAddress);
        }

        var shippingView = registry.get('checkout.steps.shipping-step.shippingAddress');

        if (shippingView && shippingView.validateShippingAddress()) {
            if ($('input[name="delivery_type"]:checked').val() == 'collect') {
                changeClickCollectLinkShow();
            } else {
                changeDeliveryLinkShow();
            }
            $("#shipping-method-buttons-container .continue").trigger("click");
            toggleShippingMethod();
        } else {
            $('input[name="street[0]"]').attr({
                'placeholder': 'Street *',
                'digidirect-autocomplete': 'on'
            });
            console.warn("Shipping view not available or validation failed.");
        }
    });
});
