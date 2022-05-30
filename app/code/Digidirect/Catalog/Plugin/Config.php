<?php
namespace Digidirect\Catalog\Plugin;

class Config
{
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        $options[] = ["latest" => __("New Products")];
        return $options;
    }
}