define([
    'jquery',
    'domReady!',
    'mage/translate',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/step-navigator',
    'uiRegistry',
    'Magento_Checkout/js/model/full-screen-loader'
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
        $('input[name="street[0]"]').attr({
            'placeholder': 'Street *',
            'digidirect-autocomplete': 'on'
        });

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

        /*if (customer.isLoggedIn()) {
            $(".field.addresses").attr("style", "display: block !important");
            $("li#shipping .action.action-show-popup").attr("style", "display: block !important");
        }*/

        fullScreenLoader.stopLoader();
    }

    function changeEmailLinkShow() {
        $("#customer-info-change-extension").removeClass('hide');
        $("#customer-info-change-extension").show();
        $("#customer-info-button-extension").hide()
    }

    $(document).on("click", "#customer-info-button-extension, #customer-info-change-extension", function () {
        fullScreenLoader.startLoader();
        const email = $('#customer-email').val();

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#customer-email-error').show();
            fullScreenLoader.stopLoader();
            console.log('Email is not in correct format');
            return; //NOT VALID EMAIL
        }

        validateEmail(email).done(function () {
            // Email is NOT registered → guest
            quote.guestEmail = email;
            checkoutData.setValidatedEmailValue(email);
            console.log('Email is NOT registered (guest)');
           // stepNavigator.next();
            //stepNavigator.setHash('shipping-address');
            changeEmailLinkShow();
            toggleShippingAddress();
            fullScreenLoader.stopLoader();
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
