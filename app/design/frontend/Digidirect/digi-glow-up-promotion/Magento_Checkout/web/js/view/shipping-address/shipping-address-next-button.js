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

            $('input[type="radio"][name^="ko_unique_"]').removeAttr('disabled');

            //toggle down payment section as shipping method section is active now
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
        // Validate Click & Collect store selection
        if ($('input[name="delivery_type"]:checked').val() == 'collect') {
            // Check if store is selected - look for checked radio OR confirm button
            var storeSelected = $('input[name="storeSelection"]:checked').length > 0 ||
                $('a.link.-collect.action.primary').length > 0;

            if (!storeSelected) {
                alert($t('Please select a store for Click & Collect.'));
                return false;
            }

            // Validate required fields for Click & Collect
            const $form = $('#shipping-new-address-form');
            const requiredFields = ['firstname', 'lastname', 'telephone'];
            const missingFields = [];

            requiredFields.forEach(function(name) {
                if (!$.trim($form.find(`input[name="${name}"]`).val())) {
                    missingFields.push(name);
                }
            });

            if (missingFields.length > 0) {
                alert($t('Please fill in: ') + missingFields.join(', '));
                return false;
            }

            // Get customer details from form
            var firstname = $.trim($form.find('input[name="firstname"]').val());
            var lastname = $.trim($form.find('input[name="lastname"]').val());
            var telephone = $.trim($form.find('input[name="telephone"]').val());

            // Use proper Click & Collect address with customer details
            var dummyAddress = {
                firstname: firstname,
                lastname: lastname,
                street: ['Click & Collect'],
                city: 'Store Pickup',
                region: 'NSW',
                regionId: 569, // NSW - adjust if needed
                regionCode: 'NSW',
                countryId: 'AU',
                postcode: '2000',
                telephone: telephone,
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
            console.warn("Shipping validation failed");
        }
    });
});
