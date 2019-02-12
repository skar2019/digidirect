<?php
namespace Ewave\GiftCardImage\Ui\Component\Listing\Columns;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Active')], ['value' => 0, 'label' => __('Inactive')]];
    }
}
