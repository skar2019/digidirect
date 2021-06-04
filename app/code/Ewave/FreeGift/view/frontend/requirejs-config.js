/* eslint no-unused-vars: [1] */
var config = {
    map: {
        'Magento_SalesRule/js/view/payment/discount': {
            'Magento_SalesRule/js/action/set-coupon-code': 'Ewave_FreeGift/js/action/set-coupon-code'
        },
        '*': {
            'Magento_SalesRule/js/action/set-coupon-code': 'Ewave_FreeGift/js/action/set-coupon-code',
            'Magento_SalesRule/js/action/cancel-coupon': 'Ewave_FreeGift/js/action/cancel-coupon',
            'freeGiftByRules': 'Ewave_FreeGift/js/free-gift-by-rules/rules',
            'Magento_Weee/js/view/checkout/summary/item/price/row_excl_tax-extend': 'Ewave_FreeGift/js/view/checkout/summary/item/price/row_excl_tax-extend'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/item/details/thumbnail': {
                'Ewave_FreeGift/js/view/summary/item/details/thumbnail': true
            }
        }
    }
};
