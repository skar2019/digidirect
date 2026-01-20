define([
    'jquery',
    'domReady!',
    'mage/translate',
    'Magento_Checkout/js/view/checkout-toggle',
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
    checkoutToggle,
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

        checkoutToggle.toggleDownAllSections();
        checkoutToggle.toggleUpCustomerInfoSection();
        checkoutToggle.toggleUpShippingAddressSection();

        checkoutToggle.showChangeEmailLink();
        checkoutToggle.showChangeShippingAddressLink();

        let target = '';

        if ($('input[name="delivery_type"]:checked').val() == 'delivery') {

            target = $('#checkoutSteps li#opc-shipping_method');

            //additional fixes for shipping method section
            //$('#s_method_standard_standard').prop('checked', false);
            //$('#s_method_express_express').prop('checked', false);
            $('input[type="radio"][name^="ko_unique_"]').removeAttr('disabled');

            //toggle down payment section (additional fix) as shipping method section is active now
            $('#checkoutSteps li#payment')
                .removeClass('active')
                .addClass('inactive')
                .find('.step-content, .action-extension-toolbar').hide();

            checkoutToggle.toggleUpShippingMethodSection();
            checkoutToggle.hideChangeShippingMethodLink();

        } else {
            if (customer.isLoggedIn()) {
                $('#payment .step-title.accordion-step').text('2. Payment');
            } else {
                $('#payment .step-title.accordion-step').text('3. Payment');
            }

            target = $('#checkoutSteps li#payment');

            checkoutToggle.toggleUpPaymentMethodSection();
        }

        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 600);
        }
    }

    $(document).on("click",
        "#clickcollect-info-change-extension, #clickcollect-store-change-extension, #delivery-info-change-extension, #delivery-address-change-extension",
        function () {
            checkoutToggle.toggleDownAllSections();
            checkoutToggle.toggleUpCustomerInfoSection();
            checkoutToggle.toggleUpShippingAddressSection();

            checkoutToggle.showChangeEmailLink();
            checkoutToggle.hideChangeShippingAddressLink();

        });

    $(document).on("click",'input[name="delivery_type"]', function () {
        if ($(this).val() == 'collect') {
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
                $('#opc-new-shipping-address').attr("style", "display: none !important");
            }

            $("#checkout-step-shipping .wrap-block").attr("style", "display: none !important");
            $('.store-locator-wrapper').attr("style", "display: none !important");
        }
    });


    $(document).on("click", "#delivery-info-button-extension", function () {
        // TODO:: ADD VALIDATION OR SETTIMEOUT HERE TO NOT TRIGGERING SELECTED STORE
        if ($('input[name="delivery_type"]:checked').val() == 'collect' && !$('a.link.-collect.action.primary').length) {
            $('input[name="storeSelection"]').prop('checked', false);
            return ;
        }

        //shipping address error fix
        if ($('input[name="delivery_type"]:checked').val() == 'collect') {

            const $form = $('#shipping-new-address-form');
            const requiredFields = ['firstname', 'lastname', 'telephone'];
            const isEmpty = requiredFields.some(name => !$.trim($form.find(`input[name="${name}"]`).val()));
            if (isEmpty) return;

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
            toggleShippingMethod();
            $("#shipping-method-buttons-container .continue").trigger("click");
        } else {
            $('input[name="street[0]"]').attr({
                'placeholder': 'Street *',
                'digidirect-autocomplete': 'on'
            });
            console.warn("Shipping view not available or validation failed.");
        }
    });
});
