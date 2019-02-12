<?php
namespace Ewave\Collect\Api;

use Ewave\Collect\Api\Data\CollectPlaceInterface;

/**
 * Interface ApplyCollectStore
 * @package Ewave\Collect\Ap
 */
interface ApplyCollectPlaceInterface
{
    /**
     * @param string $collectPlaceId
     * @param string $storageName
     * @return bool|string
     */
    public function applyCollectPlaceToAllItems($collectPlaceId, $storageName);

    /**
     * @return CollectPlaceInterface|bool
     */
    public function getSelectedSingleCollectPlace();
}
