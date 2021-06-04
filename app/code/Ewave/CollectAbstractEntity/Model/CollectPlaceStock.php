<?php
namespace Ewave\CollectAbstractEntity\Model;

use Ewave\Collect\Api\CollectPlaceStockInterface;

/**
 * Class CollectPlaceStock
 * @package Ewave\CollectAbstractEntity\Model
 */
class CollectPlaceStock implements CollectPlaceStockInterface
{
    /**
     * Get product qty by Collect Place Id
     *
     * @param string $sku
     * @param string $collectPlaceId
     * @return int
     */
    public function getProductQty($sku, $collectPlaceId)
    {
        // @todo Checking Item Qty.
        return 100;
    }
}
