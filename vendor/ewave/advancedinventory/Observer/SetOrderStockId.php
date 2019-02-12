<?php
namespace Ewave\AdvancedInventory\Observer;

use Ewave\AdvancedInventory\Api\StockResolverInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

class SetOrderStockId implements ObserverInterface
{
    /**
     * @var \Ewave\AdvancedInventory\Api\StockResolverInterface
     */
    protected $stockResolver;

    /**
     * @param \Ewave\AdvancedInventory\Api\StockResolverInterface $stockResolver
     */
    public function __construct(
        StockResolverInterface $stockResolver
    ) {
        $this->stockResolver = $stockResolver;
    }

    /**
     * @param Observer $observer
     * @return $this
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $order->setStockId($this->stockResolver->getCurrentStockId());
        return $this;
    }
}
