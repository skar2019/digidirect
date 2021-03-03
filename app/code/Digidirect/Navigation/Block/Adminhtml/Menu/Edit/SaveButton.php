<?php

namespace Digidirect\Navigation\Block\Adminhtml\Menu\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Digidirect\Navigation\Block\Adminhtml\Menu\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get save button data
     *
     * @return []
     */
    public function getButtonData()
    {
        if ($this->auth->isAllowed('Digidirect_Navigation::navigation')) {

            if ($this->getCurrentMenuItem()->isReadOnly()) {
                return [];
            }
            return [
                'label' => __('Save Menu Item'),
                'class' => 'save primary',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'save']],
                    'form-role' => 'save',
                ],
                'sort_order' => 90,
            ];
        }
        return [];
    }
}
