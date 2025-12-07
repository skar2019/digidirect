define([
    'jquery',
    'domReady!',
    'mage/translate',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/view/checkout-toggle',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/step-navigator',
    'uiRegistry'
], function (
    $,
    domReady,
    $t,
    checkEmailAvailability,
    quote,
    checkoutData,
    checkoutToggle,
    fullScreenLoader,
    stepNavigator,
    registry
) {
    'use strict';

    function validateEmail(email) {
        const deferred = $.Deferred();
        checkEmailAvailability(deferred, email);
        return deferred.promise();
    }

    function toggleUpShippingAddress() {
        // Set placeholder and autocomplete for street address
        $('input[name="street[0]"]').attr({
            'placeholder': 'Street *',
            'digidirect-autocomplete': 'on'
        });

        checkoutToggle.toggleDownAllSections();
        checkoutToggle.toggleUpCustomerInfoSection();
        checkoutToggle.toggleUpShippingAddressSection();

        checkoutToggle.showChangeEmailLink();
        checkoutToggle.hideChangeShippingAddressLink();

        $('#collect_type_delivery').prop('checked', true).trigger('change');

        if ($('#collect_type_collect').length === 0) {
            $('<style>')
                .prop('type', 'text/css')
                .html('.collect-type-delivery-label::before { display: none !important; }')
                .appendTo('head');
        }

        // Scroll to shipping section
        let target = $('#checkoutSteps li#shipping');
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 600);
        }

        // Hide shipping address change links sections
        //$("#delivery_info_change_link_section").hide();
        //$("#clickcollect_info_change_link_section").hide();

        // Show shipping address form and related sections
        //$(".collect-type").show();
        //$('#co-shipping-form').show();

    }

    $(document).on("click", "#customer-info-button-extension", function () {
        fullScreenLoader.startLoader();
        const email = $('#customer-email').val();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#customer-email-error').show();
            fullScreenLoader.stopLoader();
            return; //NOT VALID EMAIL
        }

        validateEmail(email).done(function () {
            // Email is NOT registered → guest
            quote.guestEmail = email;
            checkoutData.setValidatedEmailValue(email);
            toggleUpShippingAddress();
            fullScreenLoader.stopLoader();
        }).fail(function () {
            // Email is registered → show password
            registry.get('checkout.steps.shipping-step.customer-email', function (component) {
                component.isPasswordVisible(true);
                $('#customer-password').focus();
                fullScreenLoader.stopLoader();
            });
        });
    });

    $(document).on("click", "#customer-info-change-extension", function () {
        checkoutToggle.toggleDownAllSections();
        checkoutToggle.toggleUpCustomerInfoSection();

        checkoutToggle.hideChangeEmailLink();

    });
});
