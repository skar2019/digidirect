<?php

namespace Ewave\Navigation\Block\Adminhtml\Menu\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinueButton
 *
 * @package Ewave\Navigation\Block\Adminhtml\Menu\Edit
 */
class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Get save and continue button ui component info
     *
     * @return []
     */
    public function getButtonData()
    {
        if ($this->getCurrentMenuItem()->isReadOnly()) {
            return [];
        }
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}
