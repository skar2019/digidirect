var config = {
  config: {
    mixins: {
      'Magento_Checkout/js/view/minicart': {
        'Magento_Checkout/js/view/minicart-custom': true
      }
    }
  },
  deps: [
    'js/custom' // your existing custom file
  ]
};
