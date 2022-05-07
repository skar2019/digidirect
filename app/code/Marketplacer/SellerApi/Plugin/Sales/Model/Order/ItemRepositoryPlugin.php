<?php

namespace Marketplacer\SellerApi\Plugin\Sales\Model\Order;

use Magento\Sales\Api\Data\OrderItemExtension;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class ItemRepositoryPlugin
{
    /**
     * @var OrderItemExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @param OrderItemExtensionFactory $extensionFactory
     */
    public function __construct(OrderItemExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param OrderItemRepositoryInterface $itemRepository
     * @param OrderItemInterface $item
     * @return OrderItemInterface
     */
    public function afterGet(
        OrderItemRepositoryInterface $itemRepository,
        OrderItemInterface $item
    ) {
        $this->setExtensionAttributes($item);
        return $item;
    }

    /**
     * @param OrderItemRepositoryInterface $itemRepository
     * @param OrderItemInterface[] $items
     * @return OrderItemInterface[]
     */
    public function afterGetList(
        OrderItemRepositoryInterface $itemRepository,
        $items
    ) {
        /**
         * @var OrderItemExtension $extensionAttributes
         */
        foreach ($items as $item) {
            $this->setExtensionAttributes($item);
        }
        return $items;
    }

    /**
     * @param OrderItemInterface $item
     * @return void
     */
    protected function setExtensionAttributes(OrderItemInterface $item)
    {
        $extensionAttributes = $item->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->extensionFactory->create();
        }

        $extensionAttributes->setSellerId($item->getSellerId());
        $extensionAttributes->setSellerName($item->getSellerName());

        $item->setExtensionAttributes($extensionAttributes);
    }
}
