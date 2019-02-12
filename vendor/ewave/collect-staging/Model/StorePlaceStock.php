<?php

namespace Ewave\CollectStaging\Model;

use Ewave\Collect\Api\CollectPlaceStockInterface;

class StorePlaceStock implements CollectPlaceStockInterface
{
    /**
     * Get store stock status
     *
     * @param $sku
     * @param $storeId
     * @return mixed
     */
    public function getStockStatus($sku, $storeId)
    {

    }

    /**
     * Get product qty by store
     *
     * @param $sku
     * @param $storeId
     * @return mixed
     */
    public function getProductQty($sku, $storeId)
    {
        if ($sku == 'test_simple') {
            return 0;
        }

        return 100;
    }

}