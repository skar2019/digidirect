<?php

namespace Digidirect\Catalog\Plugin\Pricing\Price;

class FinalPrice
{
    /**
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $subject
     * @param float $result
     * @return float
     */
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        return $result * 2;
    }
}