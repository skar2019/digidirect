<?php
namespace Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_idFieldName = GiftCardImageInterface::ID;
        $this->_init(
            'Ewave\GiftCardImage\Model\GiftCardImage',
            'Ewave\GiftCardImage\Model\ResourceModel\GiftCardImage'
        );
    }
}
