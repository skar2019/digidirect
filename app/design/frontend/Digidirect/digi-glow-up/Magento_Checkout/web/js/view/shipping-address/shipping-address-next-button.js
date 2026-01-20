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
    'Magento_Checkout/js/model/address-converter',
    'Magento_Ui/js/modal/alert'
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
    addressConverter,
    alert
) {
    'use strict';

    // Configuration
    const CONFIG = {
        DELIVERY_TYPE: {
            DELIVERY: 'delivery',
            COLLECT: 'collect'
        },
        SELECTORS: {
            DELIVERY_TYPE: 'input[name="delivery_type"]',
            STORE_SELECTION: 'input[name="storeSelection"]',
            SHIPPING_FORM: '#shipping-new-address-form',
            STORE_CONFIRM_BUTTON: 'a.link.-collect.action.primary',
            SHIPPING_METHOD_SECTION: '#checkoutSteps li#opc-shipping_method',
            PAYMENT_SECTION: '#checkoutSteps li#payment',
            STORE_LOCATOR: '.store-locator-wrapper',
            ADDRESSES_FIELD: '.field.addresses',
            NEW_ADDRESS_POPUP: 'li#shipping .action.action-show-popup'
        },
        REQUIRED_FIELDS: {
            GUEST: ['firstname', 'lastname', 'street[0]', 'city', 'postcode', 'telephone', 'region_id'],
            COLLECT: ['firstname', 'lastname', 'telephone']
        },
        CLICK_COLLECT_ADDRESS: {
            street: ['Click & Collect'],
            city: 'Store Pickup',
            region: 'NSW',
            regionId: 569, // NSW region ID - update if needed
            regionCode: 'NSW',
            countryId: 'AU',
            postcode: '2000', // Sydney CBD postcode
            telephone: '1300 344 434', // DigiDirect phone
            save_in_address_book: 0
        }
    };

    /**
     * Get selected delivery type
     * @returns {string}
     */
    function getSelectedDeliveryType() {
        return $(CONFIG.SELECTORS.DELIVERY_TYPE + ':checked').val();
    }

    /**
     * Check if Click & Collect is selected
     * @returns {boolean}
     */
    function isClickAndCollect() {
        return getSelectedDeliveryType() === CONFIG.DELIVERY_TYPE.COLLECT;
    }

    /**
     * Check if a store has been selected for Click & Collect
     * @returns {boolean}
     */
    function hasStoreSelected() {
        // Check if store selection radio is checked
        const storeSelected = $(CONFIG.SELECTORS.STORE_SELECTION + ':checked').length > 0;
        // Also check for the confirm button that appears after store selection
        const confirmButtonExists = $(CONFIG.SELECTORS.STORE_CONFIRM_BUTTON).length > 0;

        return storeSelected || confirmButtonExists;
    }

    /**
     * Validate required fields in shipping form
     * @param {Array} requiredFields - Array of field names to validate
     * @returns {Object} - {isValid: boolean, missingFields: Array}
     */
    function validateRequiredFields(requiredFields) {
        const $form = $(CONFIG.SELECTORS.SHIPPING_FORM);
        const missingFields = [];

        requiredFields.forEach(function(fieldName) {
            const $field = $form.find(`input[name="${fieldName}"], select[name="${fieldName}"]`);
            const value = $.trim($field.val());

            if (!value) {
                missingFields.push(fieldName);
                // Add error class to field
                $field.addClass('mage-error');
            } else {
                // Remove error class if field is now valid
                $field.removeClass('mage-error');
            }
        });

        return {
            isValid: missingFields.length === 0,
            missingFields: missingFields
        };
    }

    /**
     * Show validation error message
     * @param {Array} missingFields - Array of missing field names
     */
    function showValidationError(missingFields) {
        const fieldLabels = {
            'firstname': $t('First Name'),
            'lastname': $t('Last Name'),
            'street[0]': $t('Street Address'),
            'city': $t('City'),
            'postcode': $t('Postcode'),
            'telephone': $t('Phone Number'),
            'region_id': $t('State/Province')
        };

        const missingFieldLabels = missingFields.map(field => fieldLabels[field] || field);

        alert({
            title: $t('Validation Error'),
            content: $t('Please fill in the following required fields: ') + missingFieldLabels.join(', ')
        });
    }

    /**
     * Create Click & Collect shipping address
     * @returns {Object} - Shipping address object
     */
    function createClickCollectAddress() {
        const $form = $(CONFIG.SELECTORS.SHIPPING_FORM);

        // Get customer details from form
        const firstname = $.trim($form.find('input[name="firstname"]').val());
        const lastname = $.trim($form.find('input[name="lastname"]').val());
        const telephone = $.trim($form.find('input[name="telephone"]').val());
        const email = quote.guestEmail || (customer.isLoggedIn() ? customer.customerData.email : '');

        // Create address with customer details but store location
        const addressData = $.extend({}, CONFIG.CLICK_COLLECT_ADDRESS, {
            firstname: firstname,
            lastname: lastname,
            telephone: telephone,
            email: email
        });

        return addressConverter.formAddressDataToQuoteAddress(addressData);
    }

    /**
     * Toggle shipping method section and scroll to target
     */
    function toggleShippingMethod() {
        checkoutToggle.toggleDownAllSections();
        checkoutToggle.toggleUpCustomerInfoSection();
        checkoutToggle.toggleUpShippingAddressSection();

        checkoutToggle.showChangeEmailLink();
        checkoutToggle.showChangeShippingAddressLink();

        let target = '';

        if (getSelectedDeliveryType() === CONFIG.DELIVERY_TYPE.DELIVERY) {
            target = $(CONFIG.SELECTORS.SHIPPING_METHOD_SECTION);

            // Enable shipping method radio buttons
            $('input[type="radio"][name^="ko_unique_"]').removeAttr('disabled');

            // Toggle down payment section as shipping method section is active now
            $(CONFIG.SELECTORS.PAYMENT_SECTION)
                .removeClass('active')
                .addClass('inactive')
                .find('.step-content, .action-extension-toolbar').hide();

            checkoutToggle.toggleUpShippingMethodSection();
            checkoutToggle.hideChangeShippingMethodLink();

        } else {
            // Click & Collect - go directly to payment
            const stepNumber = customer.isLoggedIn() ? '2' : '3';
            $(CONFIG.SELECTORS.PAYMENT_SECTION + ' .step-title.accordion-step')
                .text($t('%1. Payment').replace('%1', stepNumber));

            target = $(CONFIG.SELECTORS.PAYMENT_SECTION);

            checkoutToggle.toggleUpPaymentMethodSection();
        }

        // Scroll to target section
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 20
            }, 600);
        }
    }

    /**
     * Handle delivery type change
     */
    function handleDeliveryTypeChange() {
        if (isClickAndCollect()) {
            // Show store locator
            $(CONFIG.SELECTORS.STORE_LOCATOR).attr("style", "display: block !important");

            // Reset store selection if confirm button doesn't exist
            if (!$(CONFIG.SELECTORS.STORE_CONFIRM_BUTTON).length) {
                $(CONFIG.SELECTORS.STORE_SELECTION).prop('checked', false);
            }

            // Hide address fields
            $(CONFIG.SELECTORS.ADDRESSES_FIELD).attr("style", "display: none !important");
            $(CONFIG.SELECTORS.NEW_ADDRESS_POPUP).attr("style", "display: none !important");

        } else {
            // Show address fields for logged in users
            if (customer.isLoggedIn()) {
                $(CONFIG.SELECTORS.ADDRESSES_FIELD).attr("style", "display: block !important");
                $(CONFIG.SELECTORS.NEW_ADDRESS_POPUP).attr("style", "display: block !important");
                $('#opc-new-shipping-address').attr("style", "display: none !important");
            }

            // Hide store locator
            $("#checkout-step-shipping .wrap-block").attr("style", "display: none !important");
            $(CONFIG.SELECTORS.STORE_LOCATOR).attr("style", "display: none !important");
        }
    }

    /**
     * Handle continue to next step
     * @returns {boolean}
     */
    function handleContinueButton() {
        // Validate Click & Collect store selection
        if (isClickAndCollect()) {
            if (!hasStoreSelected()) {
                alert({
                    title: $t('Store Selection Required'),
                    content: $t('Please select a store for Click & Collect.')
                });
                return false;
            }

            // Validate required fields for Click & Collect
            const validation = validateRequiredFields(CONFIG.REQUIRED_FIELDS.COLLECT);
            if (!validation.isValid) {
                showValidationError(validation.missingFields);
                return false;
            }

            // Set Click & Collect address
            const shippingAddress = createClickCollectAddress();
            quote.shippingAddress(shippingAddress);
        }

        // Get shipping view and validate
        var shippingView = registry.get('checkout.steps.shipping-step.shippingAddress');

        if (!shippingView) {
            console.error("Shipping view not available");
            alert({
                title: $t('Error'),
                content: $t('Unable to proceed. Please refresh the page and try again.')
            });
            return false;
        }

        // Validate shipping address
        if (!shippingView.validateShippingAddress()) {
            console.warn("Shipping address validation failed");

            // Highlight street field if empty (common issue)
            const $streetField = $('input[name="street[0]"]');
            if (!$.trim($streetField.val())) {
                $streetField.addClass('mage-error').attr('placeholder', $t('Street Address *'));
            }

            return false;
        }

        // Proceed to next step
        toggleShippingMethod();
        $("#shipping-method-buttons-container .continue").trigger("click");

        return true;
    }

    /**
     * Initialize event handlers
     */
    function init() {
        // Handle change links (edit email, address, store)
        $(document).on("click",
            "#clickcollect-info-change-extension, #clickcollect-store-change-extension, #delivery-info-change-extension, #delivery-address-change-extension",
            function () {
                checkoutToggle.toggleDownAllSections();
                checkoutToggle.toggleUpCustomerInfoSection();
                checkoutToggle.toggleUpShippingAddressSection();

                checkoutToggle.showChangeEmailLink();
                checkoutToggle.hideChangeShippingAddressLink();
            }
        );

        // Handle delivery type change
        $(document).on("click", CONFIG.SELECTORS.DELIVERY_TYPE, function () {
            handleDeliveryTypeChange();
        });

        // Handle continue button
        $(document).on("click", "#delivery-info-button-extension", function (e) {
            e.preventDefault();
            handleContinueButton();
        });

        // Clear error state on field input
        $(document).on('input change', CONFIG.SELECTORS.SHIPPING_FORM + ' input, ' + CONFIG.SELECTORS.SHIPPING_FORM + ' select', function() {
            $(this).removeClass('mage-error');
        });
    }

    // Initialize on DOM ready
    init();

    // Return public methods if needed
    return {
        toggleShippingMethod: toggleShippingMethod,
        isClickAndCollect: isClickAndCollect,
        hasStoreSelected: hasStoreSelected
    };
});
