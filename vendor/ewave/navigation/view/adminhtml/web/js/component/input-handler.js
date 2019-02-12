define([
    'Magento_Ui/js/form/element/textarea',
    'jquery'
], function (Textarea, $) {
    'use strict';

    return Textarea.extend({

        defaults: {
            inputBinding: true,
            itemIdentifierInput: '',
            inputIndefierName: 'menu_item_code',
            oldIndefierValue: ''

        },

        initObservable: function () {
            this.setInputIndefier();
            return this._super();
        },

        setInputIndefier: function () {
            var self = this;

            function findInputIndefier () {
                self.itemIdentifierInput = $('[name="' + self.inputIndefierName + '"]');

                if (self.itemIdentifierInput.length) {
                    self.oldIndefierValue = self.itemIdentifierInput.val();
                    self.inputBinding = !self.itemIdentifierInput.val();
                } else {
                    setTimeout(findInputIndefier, 50);
                }
            }

            findInputIndefier();
        },

        handleNameChange: function () {
            var value = this.value(),
                identValue = value.trim().split(' ').join('_'),
                oldIndentValue = this.itemIdentifierInput.val();

            if (oldIndentValue === this.oldIndefierValue && this.inputBinding) {
                this.itemIdentifierInput.val(identValue);
                this.oldIndefierValue = identValue;
            }
        },

        endChanges: function () {
            this.itemIdentifierInput.trigger('change');
        }
    });
});
