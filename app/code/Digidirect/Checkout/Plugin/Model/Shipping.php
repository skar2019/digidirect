<?php

namespace Digidirect\Checkout\Plugin\Model;

use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;

class Shipping {
    
    public function __construct(
        GetSourceItemsBySku $getSourceItemsBySku
    ) {
        $this->getSourceItemsBySku = $getSourceItemsBySku;
    }
       
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart');

        $items = $cart->getQuote()->getAllItems();
        $qty = 1;
        
        /*foreach ($items as $item) {

            $prodId = $item->getProductId();
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $_objectManager->get('\Magento\Catalog\Model\Product')->load($prodId);

            $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

            foreach ($sourceItems as $sourceItemId => $sourceItem) {
                if ($sourceItem->getSourceCode() == 'SWHS') {
                    $qty = $qty * $sourceItem->getQuantity();
                }
            }
        }*/
        
        //if ($carrierCode == 'standard' && $qty == 0) {
        if ($carrierCode == 'standard') {
            return false;
        }
        return $proceed($carrierCode, $request);
    }
   
}