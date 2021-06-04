<?php

namespace Ewave\PreOrder\Model;

use Ewave\PreOrder\Api\Data\OrderItemPreorderInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderItemPreorder
 *
 * @package Ewave\PreOrder\Model
 */
class OrderItemPreorder extends AbstractModel implements OrderItemPreorderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderItemId()
    {
        return $this->_getData(self::ORDER_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItemId($orderItemId)
    {
        $this->setData(self::ORDER_ITEM_ID, $orderItemId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsPreorder()
    {
        return (bool)$this->_getData(self::IS_PREORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPreorder($isPreorder)
    {
        $this->setData(self::IS_PREORDER, $isPreorder);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init('Ewave\PreOrder\Model\ResourceModel\OrderItemPreorder');
    }
}
