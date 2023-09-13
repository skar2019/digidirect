<?php

namespace Digidirect\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class WiserPrice implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        //get the item just added to cart
        $item = $observer->getEvent()->getData('quote_item');
        $product = $observer->getEvent()->getData('product');
        //(optional) get the parent item, if exists
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        
        $price = $product->getData('final_price');
        $wiserPrice = $product->getData('wiser_price');
        
        if (!$product->getData('added_by_rule_id')) {
            if ($wiserPrice > 1 && !empty($wiserPrice)) {
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
            $item->getProduct()->setIsSuperMode(true);
        }

    }
}