define([
    'domReady!',
    'outstockNotification',
    'outstockConfigurable'
], function () {
    'use strict';

   var outOfStockBtn = document.querySelector('.outofstock-button.-visible-block'),
       parentSelector,wrapper,field;

   if (outOfStockBtn) {
       parentSelector = document.getElementById('product_addtocart_form');
       wrapper = parentSelector.getElementsByClassName('box-tocart')[0];
       field = wrapper.getElementsByClassName('control')[0];
       field.classList.add('-back-btn');
   }
});