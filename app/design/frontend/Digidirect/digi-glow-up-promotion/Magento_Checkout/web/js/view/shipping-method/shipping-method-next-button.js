define([
    'jquery',
    'domReady!',
    'mage/translate',
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
    quote,
    checkoutData,
    checkoutToggle,
    fullScreenLoader,
    stepNavigator,
    registry
) {
    'use strict';

    $("body").on("click", "#shipping-button-extension", function(){
        if ($('#s_method_standard_standard').is(':checked') || $('#s_method_express_express').is(':checked')) {
            checkoutToggle.toggleDownAllSections();
            checkoutToggle.toggleUpCustomerInfoSection();
            checkoutToggle.toggleUpShippingAddressSection();
            checkoutToggle.toggleUpShippingMethodSection();
            checkoutToggle.toggleUpPaymentMethodSection();

            checkoutToggle.showChangeEmailLink();
            checkoutToggle.showChangeDeliveryLink();
            checkoutToggle.showChangeShippingAddressLink();
            checkoutToggle.showChangeShippingMethodLink();

        } else {
            $('#checkoutSteps li#payment').removeClass('active').addClass('inactive');
            $('#checkoutSteps li#payment .step-content').hide();
            $('#checkoutSteps li#payment .action-extension-toolbar').hide();
        }

        $("#shipping-method-buttons-container .continue").trigger("click");

        var target = $('#checkoutSteps li#payment');

        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top
            }, 600);
        }
    });

    $(document).on("click", "#shipping-method-change-extension", function () {

        checkoutToggle.toggleDownAllSections();
        checkoutToggle.toggleUpCustomerInfoSection();
        checkoutToggle.toggleUpShippingAddressSection();
        checkoutToggle.toggleUpShippingMethodSection();

        checkoutToggle.showChangeEmailLink();
        checkoutToggle.showChangeDeliveryLink();
        checkoutToggle.showChangeShippingAddressLink();

        checkoutToggle.hideChangeShippingMethodLink();
    });
});
