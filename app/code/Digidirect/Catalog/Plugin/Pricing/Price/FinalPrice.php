<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

class FinalPrice
{
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $subject->getProduct();
        $price = $product->getData('final_price');
        $wiserPrice = number_format((float)$product->getData('wiser_price'), 2, '.', '');
        
        if ($product) {
            if ($wiserPrice > 1 && !empty($wiserPrice)) {
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