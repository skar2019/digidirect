<?php

namespace Digidirect\MyOrderItems\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface;

/**
 * OrderItemState model.
 */
class OrderItemState extends AbstractExtensibleModel implements OrderItemStateInterface
{
    /**
     * Initialize resource model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Digidirect\MyOrderItems\Model\ResourceModel\OrderItemState::class);
    }

    /**
     * @inheritdoc
     */
    public function getSalesItemId()
    {
        return $this->getData(self::SALES_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setSalesItemId($salesItemId)
    {
        return $this->setData(self::SALES_ITEM_ID, $salesItemId);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @return bool
     */
    public function hasObjectNewFlag()
    {
        return $this->_isObjectNew;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Digidirect\MyOrderItems\Api\Data\OrderItemStateExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get Date of Purchase
     *
     * @return string
     */
    public function getDateOfPurchase()
    {
        return $this->getData(self::DATE_OF_PURCHASE);
    }

    /**
     * Set Status
     *
     * @param string $date
     * @return \Digidirect\MyOrderItems\Api\Data\OrderItemStateInterface
     */
    public function setDateOfPurchase($date)
    {
        return $this->setData(self::DATE_OF_PURCHASE, $date);
    }
}
