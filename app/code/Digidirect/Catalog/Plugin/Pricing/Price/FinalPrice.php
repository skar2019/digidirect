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
        Registry $registry,
        array $data = []
    ) {
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }
    
    public function afterGetValue(\Magento\Catalog\Pricing\Price\FinalPrice $subject, $result)
    {
        $product = $this->registry->registry('current_product');
        if ($product) {
            $price = $this->catalogHelper->getPrice($product);
            $wiserPrice = $this->catalogHelper->getWiserPrice($product);
            
            if ($wiserPrice != 0 || !empty($wiserPrice)) {
                if ($wiserPrice < $price) {
                    $result = $wiserPrice;
                }
            }
            
            return $result;
        }
    }
}