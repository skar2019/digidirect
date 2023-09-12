<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\Registry;

class FinalPrice
{
    protected $registry;
    
    protected $catalogHelper;

    public function __construct(
        CatalogHelper $catalogHelper,
        Registry $registry
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
    }
    
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $this->registry->registry('current_product');
        $price = $product->getPriceInfo()->getPrice('final_price')->getValue();
        //$wiserPrice = $product->getData('wiser_price');
        
        /*if ($product) {
            if ($wiserPrice != 0 && !empty($wiserPrice)) {
                if ($wiserPrice < $price) {
                    $result = $wiserPrice;
                }
            }
        }*/
        
        $result = $price;
            
        return $result;
    }
}