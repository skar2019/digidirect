<?php

namespace Ewave\Navigation\Block\Adminhtml\Set\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Ewave\Navigation\Block\Adminhtml\Set\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * Get button ui component data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getSetId()) {
            $data = [
                'label' => __('Delete Navigation Set'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['set_id' => $this->getSetId()]);
    }
}
