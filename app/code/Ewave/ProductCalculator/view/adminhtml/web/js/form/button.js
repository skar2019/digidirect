define([
    'Magento_Ui/js/form/components/button'
], function (Button) {
    'use strict';

    return Button.extend({
        defaults: {
            elementTmpl: 'Ewave_ProductCalculator/form/element/button',
            href: '',
            buttonClasses: 'action-secondary'
        }
    });
});
