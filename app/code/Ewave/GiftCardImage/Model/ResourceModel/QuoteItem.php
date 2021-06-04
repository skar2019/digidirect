<?php
namespace Ewave\GiftCardImage\Model\ResourceModel;

use Ewave\GiftCardImage\Model\QuoteItem as QuoteItemModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class QuoteItem
 * @package Ewave\GiftCardImage\Model\ResourceModel
 */
class QuoteItem extends AbstractDb
{
    const MAIN_TABLE = 'ewave_giftcard_quote_item';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_setMainTable(self::MAIN_TABLE, QuoteItemModel::QUOTE_ITEM_ID);
    }

    /**
     * @param int $quoteItemId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGiftcardImageIdByQuoteItemId($quoteItemId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), [QuoteItemModel::GIFTCARD_IMAGE_ID])
            ->where(QuoteItemModel::QUOTE_ITEM_ID . ' = ?', $quoteItemId);
        return (int)$connection->fetchOne($select);
    }

    /**
     * @param int $quoteItemId
     * @param int $giftCardImageId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveGiftCardImage($quoteItemId, $giftCardImageId)
    {
        return $this->getConnection()->insertOnDuplicate($this->getMainTable(), [
            QuoteItemModel::QUOTE_ITEM_ID => $quoteItemId,
            QuoteItemModel::GIFTCARD_IMAGE_ID => $giftCardImageId
        ]);
    }
}
