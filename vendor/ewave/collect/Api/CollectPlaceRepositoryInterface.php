<?php

namespace Ewave\Collect\Api;

/**
 * Interface CollectPlaceRepositoryInterface
 *
 * @package Ewave\Collect\Api
 */
interface CollectPlaceRepositoryInterface
{
    /**
     * Get Collect Place by Id
     *
     * @param string $id
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface
     */
    public function getById($id);

    /**
     * Get All CollectPlaces
     *
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getAll();

    /**
     * Get collect places list by SKU
     *
     * @param string $sku
     * @param int $qty
     * @return \Ewave\Collect\Api\Data\CollectPlaceInterface[]
     */
    public function getListBySku($sku, $qty = 1);
}
