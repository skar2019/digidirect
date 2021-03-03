<?php
namespace Digidirect\Navigation\Block\Adminhtml\Set\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Digidirect\Navigation\Block\Adminhtml\Set\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get save button ui component info
     * 
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Navigation Set'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
