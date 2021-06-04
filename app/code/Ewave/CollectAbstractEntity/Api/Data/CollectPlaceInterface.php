<?php
namespace Ewave\CollectAbstractEntity\Api\Data;

use Ewave\Collect\Api\Data\CollectPlaceInterface as CollectPlaceInterfaceOriginal;

/**
 * Interface CollectPlaceInterface
 */
interface CollectPlaceInterface extends CollectPlaceInterfaceOriginal
{
    /**
     * @return string
     */
    public function getPostcode();
}
