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
        
        echo $this->console_log('final_price: '.$price);
        echo $this->console_log('wiser_price: '.$wiserPrice);

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
        $item->getProduct()->setIsSuperMode(true);

    }
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

}