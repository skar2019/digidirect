<?php
namespace Ewave\Navigation\Block\Adminhtml\Menu\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 *
 * @package Ewave\Navigation\Block\Adminhtml\Menu\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get button data
     *
     * @return []
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getCurrentMenuItem()->isReadOnly()) {
            return $data;
        }
        $menuId = $this->getMenuId();
        if ($menuId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => $this->_getConfirmMessage(),
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get default delete confirmation or custom
     *
     * @return string
     */
    protected function _getConfirmMessage()
    {
        $storeId = $this->request->getParam('store', 0);
        $params = [
            'id' => $this->getMenuId(),
            'store' => $storeId,
        ];
        $deleteUrl = $this->urlBuilder->getUrl('*/*/delete', $params);
        $defaultConfirmPopupText = 'Are you sure you want to delete this?';

        $currentMenuItem = $this->getCurrentMenuItem();
        $hasChildren = !empty($currentMenuItem->hasChildren($storeId));

        $confirmDeleteParentText = 'There are menu items assigned to this parent item.' .
            ' Are you sure you want to delete this menu item and all the menu items assigned to it?';

        return 'deleteConfirm(\'' . __(
            $hasChildren ? $confirmDeleteParentText : $defaultConfirmPopupText
        ) . '\', \'' . $deleteUrl . '\')';
    }
}
