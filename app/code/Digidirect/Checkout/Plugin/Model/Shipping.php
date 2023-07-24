<?php

namespace Digidirect\Checkout\Plugin\Model;

use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Magento\Checkout\Model\Session;
use Digidirect\CollectAbstractEntity\Helper\Places;
use Digidirect\Collect\Helper\Data;

class Shipping {
    
    public function __construct(
        Session $checkoutSession,
        Places $placesHelper,
        Data $collectHelper,
        GetSourceItemsBySku $getSourceItemsBySku
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->placesHelper = $placesHelper;
        $this->collectHelper = $collectHelper;
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
        
        foreach ($items as $item) {

            $prodId = $item->getProductId();
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $_objectManager->get('\Magento\Catalog\Model\Product')->load($prodId);

            $sourceItems = $this->getSourceItemsBySku->execute($product->getSku());

            foreach ($sourceItems as $sourceItemId => $sourceItem) {
                echo $this->console_log('Source: ' . $sourceItem->getSourceCode());
                echo $this->console_log('Qty: ' . $sourceItem->getQuantity());
                if ($sourceItem->getSourceCode() == 'SWHS') {
                    $qty = $qty * $sourceItem->getQuantity();
                }
            }
        }
        
        if ($carrierCode == 'standard' && $qty == 0) {
            return false;
        } else {
            return true;
        }
        return $proceed($carrierCode, $request);
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