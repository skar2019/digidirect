<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

class FinalPrice
{
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $subject->getProduct();
        $price = $product->getData('final_price');
        $wiserPrice = $product->getData('wiser_price');
        echo $this->console_log("final_price: " . $price);
        
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
    
    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }
}