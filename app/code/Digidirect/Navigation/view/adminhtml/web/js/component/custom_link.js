define([
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'ko',
    'uiRegistry'
], function (Abstract, $, ko, registry) {
    'use strict';

    return Abstract.extend({

        showLink: function () {
            var select = registry.get(this.imports.showLink.split(':')[0]),
                value = select.value(),
                isVisible = select.visible();

            if (isVisible && value === '0') {
                this.visible(false);
            }
            if (isVisible && value === '1') {
                this.visible(true);
                this.disabled(false);
            }
        }
    });
});
