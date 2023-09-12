<?php

namespace Digidirect\Catalog\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class Product implements ObserverInterface
{
    protected $_productFactory;
    
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->_productFactory = $productFactory;
    }

    public function execute(EventObserver $observer)
    {
        $_collection = $observer->getCollection();
        if ($_collection){
            foreach ($_collection as $_product) {
                
                $price = $_product->getData('final_price');
                $wiserPrice = $_product->getData('wiser_price');

                if ($wiserPrice != 0 && !empty($wiserPrice)) {
                    if ($wiserPrice < $price) {
                        $finalPrice = $wiserPrice;
                    } else {
                        $finalPrice = $price;
                    }
                } else {
                    $finalPrice = $price;
                }

                $_product->setFinalPrice(100);
                $_product->setTierPrice([]);
            }
        }
        }
}