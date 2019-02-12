<?php
namespace Ewave\GiftCardImage\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ResetButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class ResetButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Reset'),
            'class'      => 'reset',
            'on_click'   => 'location.reload();',
            'sort_order' => 30
        ];
    }
}
