<?php

namespace Digidirect\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class WiserPrice implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getEvent()->getData('quote_item');            
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        
        $product = $item->getProduct();
        
        $price = $product->getData('price');
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
        
        $item->setCustomPrice($finalPrice);
        $item->setOriginalCustomPrice($finalPrice);
        $product->setIsSuperMode(true);
    }

}