<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

class FinalPrice
{
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $subject->getProduct();
        $price = $product->getData('final_price');
        $wiserPrice = $product->getData('wiser_price');
        
        if ($product) {
            if ($wiserPrice != 0 && !empty($wiserPrice)) {
                if ($wiserPrice < $price) {
                    $result = $wiserPrice;
                } else {
                    $result = $price;
                }
            }
        } else {
            $result = $price;
        }
            
        return $result;
    }
}