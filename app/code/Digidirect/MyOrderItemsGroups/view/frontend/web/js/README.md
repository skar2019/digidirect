## Order Group Manage Settings
order-group-manage.js

Option | Type | Default | Description
------ | ---- | ------- | -----------
allItemsIds | array | [] | List with all items ids in group.
baseClass | string | 'group-block' | Selector Group Block
targetContentContainer | string | '#items-groups-container' | Selector for content container
addNewItemUrl | string | '' | Add new item action url.
groupId | string | '' | Current group id.
groupName | string | '[data-role="group-name"]' | Selector with current group name.
draggableItem | string | '[data-role="draggable-item"]' | Selector for init draggable widget.
dropContainer | string | '[data-role="drop-container"]' | Selector for init droppable widget.
deleteGroupConfirmationTrigger | string | '[data-role="delete-group"]' | Selector for deleting group.
alertContent | string | $t('Current Item had been already added to ') | Default text alert.
loaderConfig |  | object |  | 
| loaderContainer | string | '[data-role="drop-container"]' | Selector for loader.
| config |  | object |  | 
rename |  | object |  | 
| renameButton | string | '[data-role="rename-group"]'| Selector for 'Rename' button.
| activeClass | string | '-rename'| Active class for Rename Mode.
remove |  | object |  | 
| clearButton | string | '[data-role="clear-button"]'| Selector for button to activate Clear Mode.
| cancelButton | string | '[data-role="cancel-remove"]' | Selector for 'Cancel' button
| applyButton | string | '[data-role="apply-remove"]' | Selector for 'Apply' button.
| activeClass | string | '-clear' | Active class for Clear Mode.
| selectAllControl | string | '[data-role="select-all"]' | Selector for field 'Select All'.
| selectItemControl | string | '[data-role="remove-item"]' | Selector for field of one item.
| selectorField | string | '[data-role="remove-items-id"]' | Selector for field in form for submit data.
| activeSelectedClass | string | '-selected' | Active class if one or more items selected to remove (for show or hide Apply button).
| replaceChar | string | '&' | Replacement character.
| actionUrl | string | '' | Controller URL for remove selected items.
add |  | object |  | 
| trigger | string | '[data-role="add-to-group"]' | Selector for link to add to group.
| targetItem | string | '[data-item-id]'| Selector for item wich should be added to group.
draggableConfig |  | object |  
| helper | string | 'clone' | Type of draggable.
| containment | string | '.column.main' | Selector for draggable area.
configConfirm |  | object |  
| content | string | $t('Are you sure you want to delete your group?')| Default content for confirmation popup.
localStorageNameSpace | string | 'myItemsGroupRemoveItemsIds' | Name of local storage item for save data.
globalEventAfterUpdateContent | string | 'contentUpdated.isNeedUpdateSocial' | Global event after update content.

Full list of settings for draggable widget you can see here http://api.jqueryui.com/draggable/
Full list of settings for confirm widget you can see here https://devdocs.magento.com/guides/v2.3/javascript-dev-guide/widgets/widget_confirm.html

