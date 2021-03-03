define([
    'Magento_Ui/js/form/element/ui-select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            multiple: false,
            optgroupTmpl: 'Digidirect_Navigation/optgroup'
        }
    });
});
