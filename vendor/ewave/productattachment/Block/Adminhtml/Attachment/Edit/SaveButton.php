<?php
namespace Ewave\ProductAttachment\Block\Adminhtml\Attachment\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Ewave\ProductAttachment\Block\Adminhtml\Attachment\Edit
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
        return [
            'label' => __('Save Attachment'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
