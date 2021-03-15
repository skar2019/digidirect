<?php
namespace Digidirect\OutOfStockNotif\Helper\Catalog;

class Product extends \Magento\Catalog\Helper\Product
{
    /**
     * Flag that shows if Magento has to check product to be saleable (enabled and/or inStock)
     *
     * @var boolean
     */
    protected $_skipSaleableCheck = true;
}
