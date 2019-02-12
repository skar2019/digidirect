<?php

namespace Ewave\PreOrder\Model;

/**
 * Class OrderPreorder
 *
 * @package Ewave\PreOrder\Model
 */
class OrderPreorderService implements \Ewave\PreOrder\Api\OrderPreorderManagementInterface
{
    /**
     * @var \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface
     */
    protected $orderPreorderRepository;

    /**
     * @var \Ewave\PreOrder\Api\OrderItemPreorderRepositoryInterface
     */
    protected $orderItemPreorderRepository;

    /**
     * @var \Ewave\PreOrder\Api\Data\OrderPreorderInterfaceFactory
     */
    protected $orderPreorderFactory;

    /**
     * @var \Ewave\PreOrder\Api\Data\OrderPreorderInterfaceFactory
     */
    protected $orderItemPreorderFactory;

    /**
     * @var \Ewave\PreOrder\Helper\Data
     */
    protected $preOrderHelper;

    /**
     * OrderPreorderService constructor.
     *
     * @param \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository
     * @param \Ewave\PreOrder\Api\OrderItemPreorderRepositoryInterface $orderItemPreorderRepository
     * @param \Ewave\PreOrder\Api\Data\OrderPreorderInterfaceFactory $orderPreorderFactory
     * @param \Ewave\PreOrder\Api\Data\OrderItemPreorderInterfaceFactory $orderItemPreorderFactory
     * @param \Ewave\PreOrder\Helper\Data $preOrderHelper
     */
    public function __construct(
        \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository,
        \Ewave\PreOrder\Api\OrderItemPreorderRepositoryInterface $orderItemPreorderRepository,
        \Ewave\PreOrder\Api\Data\OrderPreorderInterfaceFactory $orderPreorderFactory,
        \Ewave\PreOrder\Api\Data\OrderItemPreorderInterfaceFactory $orderItemPreorderFactory,
        \Ewave\PreOrder\Helper\Data $preOrderHelper
    ) {
        $this->orderPreorderRepository = $orderPreorderRepository;
        $this->orderItemPreorderRepository = $orderItemPreorderRepository;
        $this->orderPreorderFactory = $orderPreorderFactory;
        $this->orderItemPreorderFactory = $orderItemPreorderFactory;
        $this->preOrderHelper = $preOrderHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function processNewPreOrder(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        $orderId = $order->getId();
        if (!$orderId) {
            return;
        }

        try {
            $this->orderPreorderRepository->getByOrderId($orderId);
        } catch (\Exception $e) {
            if ($e instanceof \Magento\Framework\Exception\NoSuchEntityException
                || $e instanceof \Magento\Framework\Exception\InputException
            ) {
                $this->saveOrderPreorderFlag($order);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Save order preorder flag
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function saveOrderPreorderFlag(\Magento\Sales\Model\Order $order)
    {
        $orderIsPreorder = $this->processOrderItems($order);

        /** @var \Ewave\PreOrder\Api\Data\OrderPreorderInterface $orderPreorder */
        $orderPreorder = $this->orderPreorderFactory->create();
        $orderPreorder->setOrderId($order->getId());
        $orderPreorder->setIsPreorder($orderIsPreorder);
        if ($orderIsPreorder) {
            $warningText = $this->preOrderHelper->getConfig()->getOrderPreorderWarning();
            $orderPreorder->setWarning($warningText);
        }

        $this->orderPreorderRepository->save($orderPreorder);
    }

    /**
     * Process order items
     *
     * @param \Magento\Sales\Model\Order $order
     * @return bool
     */
    protected function processOrderItems(\Magento\Sales\Model\Order $order)
    {
        $orderIsPreorder = false;
        /** @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection $itemCollection */
        $itemCollection = $order->getItemsCollection();
        foreach ($itemCollection as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            $orderItemIsPreorder = $this->preOrderHelper->isOrderItemPreorder($item);
            $this->saveOrderItemPreorderFlag($item, $orderItemIsPreorder);
            $orderIsPreorder |= $orderItemIsPreorder;
        }

        return $orderIsPreorder;
    }

    /**
     * Save order item preorder flag
     *
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param bool $isPreorder
     * @return void
     */
    protected function saveOrderItemPreorderFlag(\Magento\Sales\Model\Order\Item $orderItem, $isPreorder)
    {
        /** @var \Ewave\PreOrder\Api\Data\OrderItemPreorderInterface $orderItemPreorder */
        $orderItemPreorder = $this->orderItemPreorderFactory->create();
        $orderItemPreorder->setOrderItemId($orderItem->getId());
        $orderItemPreorder->setIsPreorder($isPreorder);
        $this->orderItemPreorderRepository->save($orderItemPreorder);
    }
}
