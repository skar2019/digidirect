<?php

namespace Ewave\CriteoOneTag\Block;

/**
 * Class Order
 *
 * @package Ewave\CriteoOneTag\Block
 */
class Order extends AbstractTag
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Order constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $registry, $serializer, $data);
    }

    /**
     * @return mixed
     */
    public function getIdType()
    {
        return $this->getData('id_type');
    }

    /**
     * @return string
     */
    public function getItem()
    {
        $orderIds = $this->getData('order_ids');
        if (empty($orderIds) || !is_array($orderIds)) {
            return '';
        }
        $items = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orderIds as $orderId) {
            $order = $this->orderRepository->get((int)$orderId);
            $cart = [];
            foreach ($order->getAllVisibleItems() as $item) {
                /** @var \Magento\Sales\Model\Order\Item $item */
                $sku = $item->getSku();
                if ($product = $item->getProduct()) {
                    $sku = $product->getData('sku');
                }
                $cart[] =  $this->getIdPriceQuantity(
                    $sku,
                    $item->getPriceInclTax(),
                    $item->getQtyOrdered()
                );
            }
            $stringItem = $this->getStringItem(
                $this->getDataType(),
                $this->serializer->serialize($cart)
            );
            $stringItem .= $this->getStringItem(
                $this->getIdType(),
                "'" . $order->getIncrementId() . "'"
            );
            $items[] = $stringItem;
        }
        return implode(", ", $items);
    }
}
