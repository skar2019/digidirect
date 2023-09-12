<?php

namespace Digidirect\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;

class FinalPriceObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $price = $product->getData('final_price');
        $wiserPrice = $product->getData('wiser_price');

        if ($wiserPrice != 0 && !empty($wiserPrice)) {
            if ($wiserPrice < $price) {
                $finalPrice = $wiserPrice;
            } else {
                $finalPrice = $price;
            }
        } else {
            $finalPrice = $price;
        }
        
        $product->setFinalPrice(100);

        return $this;
    }
}