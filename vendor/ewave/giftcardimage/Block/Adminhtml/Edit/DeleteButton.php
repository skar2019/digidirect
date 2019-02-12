<?php
namespace Ewave\GiftCardImage\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 * @package Ewave\GiftCardImage\Block\Adminhtml\Edit
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($id = $this->getGiftCardImageId()) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', '
                                . '\'' . $this->getDeleteUrl($id) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get delete url
     * @param int $id
     * @return string
     */
    public function getDeleteUrl($id)
    {
        return $this->getUrl('*/*/delete', ['giftcard_image_id' => $id]);
    }
}
