define([
    'jquery',
    'underscore',
    'mage/translate',
    'Magento_Ui/js/modal/alert',    
    'Magento_Ui/js/modal/confirm',
    'jquery/jquery-storageapi',
    'jquery/ui',
    'loader'
], function ($, _, $t, alert, confirm) {
    'use strict';

    $.widget('digidirect.orderGroupManage', {
        options: {
            allItemsIds: [],
            baseClass: 'group-block',
            targetContentContainer: '#items-groups-container',
            addNewItemUrl: '',
            groupId: '',
            groupName: '[data-role="group-name"]',
            draggableItem: '[data-role="draggable-item"]',
            dropContainer: '[data-role="drop-container"]',
            deleteGroupConfirmationTrigger: '[data-role="delete-group"]',
            alertContent: $t('Current Item had been already added to '),
            loaderConfig: {
                loaderContainer: '[data-role="drop-container"]',
                config: {
                    icon: '',
                    texts: {
                        loaderText: ''
                    }
                }
            },
            rename: {
                renameButton: '[data-role="rename-group"]',
                activeClass: '-rename'
            },
            remove: {
                clearButton: '[data-role="clear-button"]',
                cancelButton: '[data-role="cancel-remove"]',
                applyButton: '[data-role="apply-remove"]',
                actionForm: '#group-action-clear',
                activeClass: '-clear',
                selectAllControl: '[data-role="select-all"]',
                selectItemControl: '[data-role="remove-item"]',
                selectorField: '[data-role="remove-items-id"]',
                activeSelectedClass: '-selected',
                replaceChar: '&'
            },
            add: {
                trigger: '[data-role="add-to-group"]',
                targetItem: '[data-item-id]'
            },
            draggableConfig: {
                helper: 'clone',
                containment: '.column.main'
            },
            configConfirm: {
                content: $t('Are you sure you want to delete your group?'),
                buttons: [{
                    text: $t('No, take me back'),
                    class: 'action-secondary',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('Yes, I\'m sure'),
                    class: 'action-secondary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            },
            localStorageNameSpace: 'myItemsGroupRemoveItemsIds',
            globalEventAfterUpdateContent: 'contentUpdated.isNeedUpdateSocial'
        },

        defaultClearButtonText: null,
        savedCheckedItemsIds: null,

        _create: function () {
            this.initWidgets();
            this._bind();
            this.checkSavedData();
        },

        /**
         * Init widgets
         */
        initWidgets: function () {
            $(this.options.draggableItem).draggable(this.options.draggableConfig);
            $(this.options.dropContainer).droppable({
                drop: $.proxy(this.dropItem, this)
            });
            this.loader = $(this.options.loaderConfig.loaderContainer).loader(this.options.loaderConfig.config);
        },

        /**
         * Bind events
         * @private
         */
        _bind: function () {
            this.element.on('click', this.options.rename.renameButton, $.proxy(this.activateRenameMode, this));
            this.element.on('click', this.options.remove.clearButton, $.proxy(this.activateClearMode, this));
            this.element.on('click', this.options.remove.cancelButton, $.proxy(this.clearAllModes, this));
            this.element.on('click', this.options.remove.applyButton, $.proxy(this.submitRemoveForm, this));
            this.element.on('click', this.options.deleteGroupConfirmationTrigger, $.proxy(this.deleteGroupConfirmation, this));
            this.element.on('change', this.options.remove.selectAllControl, $.proxy(this.setRemoveAllItems, this));
            this.element.on('change', this.options.remove.selectItemControl, $.proxy(this.changeItemClearStatus, this));
            $(this.options.add.trigger).on('click', $.proxy(this.addItemToGroup, this));
        },

        /**
         * Check saved data
         */
        checkSavedData: function () {
            this.savedCheckedItemsIds = this.getSavedItemsId();
            if (this.savedCheckedItemsIds.length) {
                this.activateClearMode();
                this.setItemsClearState();
                this.countRemoveItems();
            }
        },

        /**
         * Unbind events
         * @private
         */
        _unBind: function () {
            $(this.options.add.trigger).off('click');
        },

        /**
         * Activate Rename Mode
         * @param e
         */
        activateRenameMode: function (e) {
            this.element.addClass(this.options.rename.activeClass);
            e.preventDefault();
        },

        /**
         * Get saved items id list
         * @returns {Array}
         */
        getSavedItemsId: function () {
            return $.localStorage.get(this.options.localStorageNameSpace) || [];
        },

        /**
         * Set saved items id list
         * @param {array} data
         */
        setSavedItemsId: function (data) {
            this.savedCheckedItemsIds = data;
            $.localStorage.set(this.options.localStorageNameSpace, this.savedCheckedItemsIds);
        },

        /**
         * Clear saved items id data
         */
        clearSavedItemsId: function () {
            this.savedCheckedItemsIds = [];
            $.localStorage.remove(this.options.localStorageNameSpace);
        },

        /**
         * Activate Clear Mode
         * @param e
         */
        activateClearMode: function (e) {
            this.clearAllModes();
            this.element.addClass(this.options.remove.activeClass);
            if (e) {
                e.preventDefault();
            }
        },

        /**
         * Set clear state for visible items
         */
        setItemsClearState: function () {
            var self = this;
            this.element.find(this.options.remove.selectItemControl).each(function () {
                var id = $(this).data('id-value');
                if (_.contains(self.savedCheckedItemsIds, id.toString())) {
                    $(this).prop('checked', true);
                }
            });
        },

        /**
         * Clear All Modes
         */
        clearAllModes: function () {
            var classList = this.element.attr('class').split(' ');
            classList.map(function (itemClass) {
                if (itemClass !== this.options.baseClass) {
                    this.element.removeClass(itemClass);
                    if (itemClass === this.options.remove.activeClass) {
                        this.clearSelectedItems();
                        this.clearSavedItemsId();
                    }
                }
            }, this);
        },

        /**
         * Count items for remove
         */
        countRemoveItems: function () {
            var countItems = this.options.allItemsIds.length,
                countSelectedItems = this.savedCheckedItemsIds ? this.savedCheckedItemsIds.length : 0;

            this.setRemoveApplyButtonLabel(countSelectedItems);
            this.setRemoveAllItemsControl(countSelectedItems === countItems);
        },

        /**
         * Change item clear status
         * @param e
         */
        changeItemClearStatus: function (e) {
            var control = $(e.currentTarget),
                isChecked = control.prop('checked'),
                id = $(control).data('id-value'),
                position;

            if (isChecked) {
                this.savedCheckedItemsIds.push(id.toString());
            } else {
                position = this.savedCheckedItemsIds.indexOf(id.toString());
                this.savedCheckedItemsIds.splice(position, 1);
            }
            this.setSavedItemsId(this.savedCheckedItemsIds);
            this.countRemoveItems();
        },

        /**
         * Clear all selections
         */
        clearSelectedItems: function () {
            this.element.find(this.options.remove.selectItemControl).prop('checked', false);
            this.element.find(this.options.remove.selectAllControl).prop('checked', false);
        },

        /**
         * Set pselections for all items
         * @param e
         */
        setRemoveAllItems: function (e) {
            var isChecked = $(e.currentTarget).prop('checked');
            this.element.find(this.options.remove.selectItemControl).prop('checked', isChecked);
            this.setSavedItemsId(isChecked ? this.options.allItemsIds : []);
            this.countRemoveItems();
        },

        /**
         * Change selection for field 'Select All'
         * @param flag
         */
        setRemoveAllItemsControl: function (flag) {
            this.element.find(this.options.remove.selectAllControl).prop('checked', flag);
        },

        /**
         * Set counter and show or hide apply button
         * @param count
         */
        setRemoveApplyButtonLabel: function (count) {
            if (count > 0) {
                this.element.find(this.options.remove.applyButton).text(this.getApplyButtonDefaultText(count));
                this.element.addClass(this.options.remove.activeSelectedClass);
            } else {
                this.element.removeClass(this.options.remove.activeSelectedClass);
            }
        },

        /**
         * Get actual text for apply button
         * @param count
         * @returns {string}
         */
        getApplyButtonDefaultText: function (count) {
            var text;
            if (!this.defaultClearButtonText) {
                this.defaultClearButtonText = this.element.find(this.options.remove.applyButton).text();
            }
            text = this.defaultClearButtonText.split(this.options.remove.replaceChar);
            return text.join(count);
        },

        /**
         *  Submit form
         */
        submitRemoveForm: function () {
            $(this.options.remove.selectorField).val('[' + this.savedCheckedItemsIds + ']');
            this.clearSavedItemsId();
            $(this.options.remove.actionForm).submit();
        },

        /**
         * Delete Group Confirmation
         * @param e
         */
        deleteGroupConfirmation: function (e) {
            var self = this;

            e.preventDefault();

            confirm({
                content: this.options.configConfirm.content,
                buttons: this.options.configConfirm.buttons,
                actions: {
                    confirm: function (e) {               
                        window.location.href = $(self.options.deleteGroupConfirmationTrigger).attr('href');
                    }
                }
            });
        },

        /**
         * Drop event callback
         * @param e
         * @param ui
         */
        dropItem: function (e, ui) {
            var item = ui.helper,
                itemId = item[0].attributes['data-item-id'].value;
            if (this.isNewItem(itemId)) {
                this.addNewItem(itemId);
            } else {
                this.showErrorAlert();
            }
            this.clearAllModes();
        },

        /**
         * Show error alert
         */
        showErrorAlert: function (err) {
            var content = err || this.options.alertContent + $(this.options.groupName).text();
            alert({
                content: content
            });
        },

        /**
         * Check item in block
         * @param itemId
         * @returns {boolean}
         */
        isNewItem: function (itemId) {
            return !this.element.find('[data-item-id="' + itemId + '"]').length;
        },

        /**
         * Add new item to group
         * @param itemId, groupId
         */
        addNewItem: function (itemId, groupId) {
            var id = groupId || this.options.groupId;
            this.startLoader();
            return $.post(this.options.addNewItemUrl, {sales_item_id: itemId, group_id: id})
                .done($.proxy(this.successSendData, this, id))
                .fail($.proxy(this.errorSendData, this));
        },

        /**
         * Success send data
         * @param response
         */
        successSendData: function (id, response) {
            if (id === this.options.groupId && response['body']) {
                this._unBind();
                this.updateContent(response['body']);
            }

            if (response['message']) {
                if (response['message']['error']) {
                    this.showErrorAlert(response['message']['error']);
                } else {
                    this.onSuccessMessage(response['message']['success']);
                }
            }

            if (parseInt(id) === 0) {
                this.reloadPage();
            }

            this.stopLoader();
        },

        /**
         * Reload page
         */
        reloadPage: function () {
            window.location.reload();
        },

        /**
         * Error send data
         */
        errorSendData: function () {
            this.stopLoader();
            console.warn('Something went wrong');
        },

        /**
         * Update content
         * @param content
         */
        updateContent: function (content) {
            $(this.options.targetContentContainer).html(content).trigger('contentUpdated');
            $(document).trigger(this.options.globalEventAfterUpdateContent);
        },

        /**
         * Add item to group
         * @param e
         */
        addItemToGroup: function (e) {
            var el = $(e.target),
                itemId = el.closest(this.options.add.targetItem).attr('data-item-id'),
                groupId = el.attr('data-group-id');

            if (groupId === this.options.groupId) {
                if (this.isNewItem(itemId)) {
                    this.addNewItem(itemId);
                } else {
                    this.showErrorAlert();
                }
            } else {
                this.addNewItem(itemId, groupId);
            }

            e.preventDefault();
        },

        /**
         * Success Message
         * @param message
         */
        onSuccessMessage: function (message) {},

        /**
         * Start loader
         */
        startLoader: function () {
            this.loader.trigger('processStart');
        },

        /**
         * Stop loader
         */
        stopLoader: function () {
            this.loader.trigger('processStop');
        }
    });

    return $.digidirect.orderGroupManage;
});
