/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'sidebar': 'Magento_Checkout/js/addons/sidebar/index',
            'sidebarProductQtyAddon': 'Magento_Checkout/js/addons/sidebar/product-qty',
            'sidebarCartItemOptionsAddon': 'Magento_Checkout/js/addons/sidebar/cart-item-options'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Magento_Checkout/js/view/billing-address-extend': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'Magento_Checkout/js/view/payment/default-mixin': true
            },
            'Magento_Checkout/js/sidebar': {
                'Magento_Checkout/js/sidebar-mixin': true
            }
        }
    }
};
