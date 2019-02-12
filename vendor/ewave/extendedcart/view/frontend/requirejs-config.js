/* eslint no-unused-vars: [1] */
var config = {
    map: {
        '*': {
            'confirmationPopup': 'Ewave_ExtendedCart/js/confirmation-popup',
            'ajaxCartQty': 'Ewave_ExtendedCart/js/ajax-cart-qty'
        }
    },
    config: {
        mixins: {
            'js/extends/catalog-add-to-cart': {
                'Ewave_ExtendedCart/js/catalog-add-to-cart-extend': true
            }
        }
    }
};
