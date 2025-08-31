define([
    'jquery',
    'domReady!',
    'mage/translate',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
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

    function toggleShippingAddress() {
        $('#checkoutSteps li').removeClass('active').addClass('inactive');
        $('.opc-wrapper .step-content').hide();
        $('.opc-wrapper li .action-extension-toolbar').hide();

        $('#checkoutSteps li#customer-info').removeClass('inactive').addClass('active');
        $('#checkoutSteps li#customer-info .step-content').show();
        $('#checkoutSteps li#customer-info').css('border', 'none');
        $('#checkoutSteps  li#customer-info .action-extension-toolbar').show();

        $('#checkoutSteps li#shipping').removeClass('inactive').addClass('active');
        $('#checkoutSteps li#shipping .step-content').show();
        $('#checkoutSteps li#shipping').css('border', 'none');
        $('#checkoutSteps  li#shipping .action-extension-toolbar').show();

    }

    $(document).on("click", "#customer-info-button-extension", function () {
        const email = $('#customer-email').val();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            return; //NOT VALID EMAIL
        }

        fullScreenLoader.startLoader();

        validateEmail(email).done(function () {
            // Email is NOT registered → guest
            quote.guestEmail = email;
            checkoutData.setValidatedEmailValue(email);
            fullScreenLoader.stopLoader();
            console.log('Email is NOT registered (guest)');
           // stepNavigator.next();
            //stepNavigator.setHash('shipping-address');
            toggleShippingAddress();
        }).fail(function () {
            // Email is registered → show password
            registry.get('checkout.steps.shipping-step.customer-email', function (component) {
                component.isPasswordVisible(true);
                $('#customer-password').focus();
                console.log('Email IS registered');
                fullScreenLoader.stopLoader();
            });
        });
    });
});
