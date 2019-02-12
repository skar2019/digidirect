<?php
namespace Ewave\GiftCardImage\Model\ResourceModel;

use Ewave\GiftCardImage\Model\OrderItem as OrderItemModel;
use Ewave\GiftCardImage\Model\ResourceModel\QuoteItem as QuoteItemResource;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class OrderItem
 * @package Ewave\GiftCardImage\Model\ResourceModel
 */
class OrderItem extends AbstractDb
{
    const MAIN_TABLE = 'ewave_giftcard_order_item';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_setMainTable(self::MAIN_TABLE, OrderItemModel::ORDER_ITEM_ID);
    }

    /**
     * @param int $orderItemId
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGiftcardImageIdByOrderItemId($orderItemId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), [OrderItemModel::GIFTCARD_IMAGE_ID])
            ->where(OrderItemModel::ORDER_ITEM_ID . ' = ?', $orderItemId);
        return (int)$connection->fetchOne($select);
    }

    /**
     * @param array $items
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function moveDataFromQuoteToOrder(array $items)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['qi' => $this->getTable(QuoteItemResource::MAIN_TABLE)], [
                OrderItemModel::GIFTCARD_IMAGE_ID => OrderItemModel::GIFTCARD_IMAGE_ID,
                OrderItemModel::ORDER_ITEM_ID => new \Zend_Db_Expr('oi.item_id')
            ])
            ->join(
                ['oi' => $this->getTable('sales_order_item')],
                'qi.quote_item_id = oi.quote_item_id',
                []
            )
            ->where('qi.quote_item_id IN (?)', $items);

        $query = $connection->insertFromSelect($select, $this->getMainTable(), [
            OrderItemModel::GIFTCARD_IMAGE_ID,
            OrderItemModel::ORDER_ITEM_ID
        ]);

        return $connection->query($query);
    }
}
