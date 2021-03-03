define([
    'Magento_Ui/js/form/element/select',
    'jquery'
], function (Select, $) {
    'use strict';

    return Select.extend({
        /**
         * Filters options depending on Advanced Attribute choose
         * @param element
         * @param event
         */
        optionFilter: function (element, event) {
            var selectedValue = event.target.value,
                showingSelects,
                // 190 is <argument name="sortOrder"> of option_ids
                secondSelect = this.containers[0]._elems[190],
                // cacheOptions.plain list of all possible options
                secondOptionsList = secondSelect.cacheOptions.plain,
                optionsList = [],
                attributsObject = $.parseJSON(window.abstractAttributesConfig);
            // if this is page loading, don't reset value
            if ($('#' + secondSelect.uid).length) {
                secondSelect.value('');
            }
            if (attributsObject.hasOwnProperty(selectedValue)) {
                showingSelects = attributsObject[selectedValue];

                for (var i = 0, length = secondOptionsList.length, option; i < length; i++) {
                    option = secondOptionsList[i];
                    if (showingSelects.indexOf(option.value) !== -1) {
                        optionsList.push(option)
                    }
                }

                secondSelect.options(optionsList);
            } else {
                secondSelect.options(secondOptionsList);
            }
        }
    });
});
