var config = {
  deps: [
      'js/custom',
      'Digidirect_HelloBar/js/hello-bar',
      'Magento_Theme/js/custom-footer'
  ],
  paths: {
      'mage/calendar': false
  },
  map: {
      '*': {
          'Magento_Theme/js/view/messages': 'Digidirect_Utilities/js/view/messages'
      }
  }
};