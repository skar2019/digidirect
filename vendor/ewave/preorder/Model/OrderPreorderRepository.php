<?php

namespace Ewave\PreOrder\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Metadata;

/**
 * Class OrderPreorderRepository
 *
 * @package Ewave\PreOrder\Model
 */
class OrderPreorderRepository implements \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Metadata
     */
    protected $metadata;

    /**
     * @var \Ewave\PreOrder\Api\Data\OrderPreorderInterface[]
     */
    protected $registry;

    /**
     * OrderPreorderRepository constructor.
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
    public function save(\Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder)
    {
        try {
            $this->metadata->getMapper()->save($orderPreorder);
            $this->registry[$orderPreorder->getId()] = $orderPreorder;
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save order PreOrder'), $e);
        }
        return $this->registry[$orderPreorder->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$id) {
            throw new InputException(__('ID required'));
        }
        if (!isset($this->registry[$id])) {
            /** @var \Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder */
            $orderPreorder = $this->metadata->getNewInstance()->load($id);
            if (!$orderPreorder->getId()) {
                throw new NoSuchEntityException(__('Requested order PreOrder doesn\'t exist'));
            }
            $this->registry[$id] = $orderPreorder;
        }
        return $this->registry[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function getByOrderId($orderId)
    {
        if (!$orderId) {
            throw new InputException(__('Order ID required'));
        }

        return $this->get($this->metadata->getMapper()->getIdByOrderId($orderId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder)
    {
        try {
            $this->metadata->getMapper()->delete($orderPreorder);
            unset($this->registry[$orderPreorder->getId()]);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete order PreOrder'), $e);
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
    public function deleteByOrderId($orderId)
    {
        return $this->delete($this->getByOrderId($orderId));
    }

    /**
     * Is order has PreOrder flag
     *
     * @param int $orderId
     * @return bool
     */
    public function isOrderHasPreorderFlag($orderId)
    {
        try {
            $orderPreorder = $this->getByOrderId($orderId);
            return $orderPreorder->getIsPreorder();
        } catch (\Exception $e) {
            //Do nothing
        }
        return false;
    }
}
