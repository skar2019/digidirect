<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\Price\AbstractPrice;

class FinalPrice
{
    protected $registry;
    
    protected $catalogHelper;
    
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $price = $this->product->getData('final_price');
        $wiserPrice = $this->product->getData('wiser_price');

        if ($wiserPrice != 0 && !empty($wiserPrice)) {
            if ($wiserPrice < $price) {
                $result = $wiserPrice;
            }
        }
        return $result;
    }
}