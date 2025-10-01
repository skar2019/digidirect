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
        $('input[name="street[0]"]').attr({
            'placeholder': 'Street *',
            'digidirect-autocomplete': 'on'
        });

        $('#checkoutSteps li').removeClass('active').addClass('inactive');
        $('.opc-wrapper .step-content').hide();
        $('.opc-wrapper li .action-extension-toolbar').hide();

        $('#checkoutSteps li#customer-info')
            .removeClass('inactive')
            .addClass('active')
            .css('border', 'none')
            .find('.step-content, .action-extension-toolbar').show();

        $('#checkoutSteps li#shipping')
            .removeClass('inactive')
            .addClass('active')
            .css('border', 'none')
            .find('.step-content, .action-extension-toolbar').show();

        fullScreenLoader.stopLoader();

        let target = $('#checkoutSteps li#shipping');

        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 600);
        }
    }

    function changeEmailLinkShow() {
        $("#customer-info-change-extension").removeClass('hide').show();
        $("#customer-info-button-extension").hide();
       // $("#checkout-step-customerinfo .form-login").hide();
        $('input[name="username"]').prop('disabled', true).css('color', '#AEAEB2');
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
            changeEmailLinkShow();
            toggleShippingAddress();
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
        $("#customer-info-change-extension").hide();
        $("#customer-info-button-extension").show();
        $('input[name="username"]').prop('disabled', false).css('color', '#1d1d1f');

        /*$('#checkoutSteps li').removeClass('active').addClass('inactive');
        $('.opc-wrapper .step-content').hide();
        $('.opc-wrapper li .action-extension-toolbar').hide();

        $('#checkoutSteps li#customer-info').removeClass('inactive').addClass('active');
        $('#checkoutSteps li#customer-info .step-content').show();
        $('#checkoutSteps li#customer-info .action-extension-toolbar').show();

        $('#checkoutSteps li#customer-info')
            .removeClass('inactive')
            .addClass('active')
            .find('.step-content, .action-extension-toolbar').show();
        */
    });
});
