<?php
namespace Ewave\ProductCalculator\Block\Adminhtml\Calculator\Edit;

use Ewave\ProductCalculator\Block\Adminhtml\Form\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 * @package Ewave\ProductCalculator\Block\Adminhtml\Calculator\Edit
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Product Finder'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
