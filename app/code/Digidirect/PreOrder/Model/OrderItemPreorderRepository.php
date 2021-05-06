<?php

namespace Digidirect\PreOrder\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Metadata;

/**
 * Class OrderItemPreorderRepository
 *
 * @package Digidirect\PreOrder\Model
 */
class OrderItemPreorderRepository implements \Digidirect\PreOrder\Api\OrderItemPreorderRepositoryInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Metadata
     */
    protected $metadata;

    /**
     * @var \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface[]
     */
    protected $registry;

    /**
     * OrderItemPreorderRepository constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Metadata $metadata
     */
    public function __construct(
        Metadata $metadata
    ) {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemItemPreorder)
    {
        try {
            $this->metadata->getMapper()->save($orderItemItemPreorder);
            $this->registry[$orderItemItemPreorder->getId()] = $orderItemItemPreorder;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save order item PreOrder'), $e);
        }
        return $this->registry[$orderItemItemPreorder->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('Id required'));
        }
        if (!isset($this->registry[$id])) {
            /** @var \Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder */
            $orderItemPreorder = $this->metadata->getNewInstance()->load($id);
            if (!$orderItemPreorder->getId()) {
                throw new NoSuchEntityException(__('Requested order item PreOrder doesn\'t exist'));
            }
            $this->registry[$id] = $orderItemPreorder;
        }
        return $this->registry[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrderItemId($orderId)
    {
        if (!$orderId) {
            throw new InputException(__('Order item id required'));
        }

        return $this->get($this->metadata->getMapper()->getIdByOrderItemId($orderId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Digidirect\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder)
    {
        try {
            $this->metadata->getMapper()->delete($orderItemPreorder);
            unset($this->registry[$orderItemPreorder->getId()]);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete order item PreOrder'), $e);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        return $this->delete($this->get($id));
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByOrderItemId($orderItemId)
    {
        return $this->delete($this->getByOrderItemId($orderItemId));
    }

    /**
     * {@inheritdoc}
     */
    public function isOrderItemHasPreorderFlag($orderItemId)
    {
        try {
            $orderItemPreorder = $this->getByOrderItemId($orderItemId);
            return $orderItemPreorder->getIsPreorder();
        } catch (\Exception $e) {
            return false;
        }
    }
}
