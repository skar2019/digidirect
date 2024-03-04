<?php
namespace Digidirect\ExtendedCatalogPriceRule\Observer;

use Digidirect\ExtendedCatalogPriceRule\Helper\ViewData;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CatalogBlockProductListCollectionObserver
 * @package Digidirect\ExtendedCatalogPriceRule\Observer
 */
class CatalogBlockProductListCollectionObserver implements ObserverInterface
{
    /**
     * @var ViewData
     */
    protected $helper;

    /**
     * CatalogBlockProductListCollectionObserver constructor.
     * @param ViewData $helper
     */
    public function __construct(
        ViewData $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $productCollection = $observer->getEvent()->getCollection();
        if (!$productCollection instanceof \Magento\Catalog\Model\ResourceModel\Product\Collection) {
            return;
        }

        foreach ($productCollection as $item) {
            if (!empty($ruleData[$item->getId()])) {
                $item->setData('show_msg_extended_rule', $ruleData[$item->getId()]);
            }
        }
    }
}
