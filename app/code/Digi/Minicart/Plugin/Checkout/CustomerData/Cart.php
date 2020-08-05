<?php
namespace Digi\Minicart\Plugin\Checkout\CustomerData;

class Cart {
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        $result['qantas_points'] = $result['subtotalAmount'] ;
        
          return ($result);
    }
}