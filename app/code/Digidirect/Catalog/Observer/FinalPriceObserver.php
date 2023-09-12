<?php

namespace Digidirect\Catalog\Observer;

class FinalPriceObserver
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
        
        $product->setFinalPrice($finalPrice);

        return $this;
    }
}