<?php

namespace Ewave\PreOrder\Model;

/**
 * Class OrderPreorder
 *
 * @package Ewave\PreOrder\Model
 */
class OrderPreorder extends \Magento\Framework\Model\AbstractModel
    implements \Ewave\PreOrder\Api\Data\OrderPreorderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderId()
    {
        return $this->_getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        $this->setData(self::ORDER_ID, $orderId);
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
    public function getWarning()
    {
        return $this->_getData(self::WARNING);
    }

    /**
     * {@inheritdoc}
     */
    public function setWarning($warning)
    {
        $this->setData(self::WARNING, $warning);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init('Ewave\PreOrder\Model\ResourceModel\OrderPreorder');
    }
}
