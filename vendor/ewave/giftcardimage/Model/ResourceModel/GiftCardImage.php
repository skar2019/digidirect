<?php
namespace Ewave\GiftCardImage\Model\ResourceModel;

use Ewave\GiftCardImage\Api\Data\GiftCardImageInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class GiftCardImage
 * @package Ewave\GiftCardImage\Model\ResourceModel
 */
class GiftCardImage extends AbstractDb
{
    const MAIN_TABLE = 'ewave_giftcard_image';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, GiftCardImageInterface::ID);
        $this->_useIsObjectNew = true;
    }
}
