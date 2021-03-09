<?php

namespace Digidirect\PreOrder\Plugin;

/**
 * Class SalesOrderItem
 *
 * @package Digidirect\PreOrder\Plugin
 */
class SalesOrderItem
{
    /**
     * @var \Digidirect\PreOrder\Api\OrderItemPreorderRepositoryInterface
     */
    private $orderItemPreorderRepository;

    /**
     * SalesOrderItem constructor.
     *
     * @param \Digidirect\PreOrder\Api\OrderItemPreorderRepositoryInterface $orderItemPreorderRepository
     */
    public function __construct(
        \Digidirect\PreOrder\Api\OrderItemPreorderRepositoryInterface $orderItemPreorderRepository
    ) {
        $this->orderItemPreorderRepository = $orderItemPreorderRepository;
    }

    /**
     * Add "(Preorder)" to name if order item has preorder flag
     *
     * @param \Magento\Sales\Model\Order\Item $subject
     * @param string $result
     * @return string
     */
    public function afterGetName(\Magento\Sales\Model\Order\Item $subject, $result)
    {
        $preorderFlag = $this->orderItemPreorderRepository->isOrderItemHasPreorderFlag($subject->getId());
        if ($preorderFlag) {
            $result .= ' ' . __('(Preorder)');
        }
        return $result;
    }
}
