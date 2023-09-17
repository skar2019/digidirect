<?php
 
namespace Digidirect\Catalog\Plugin\Model;
 
class Product
{
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        return $result + 100;
    }
}