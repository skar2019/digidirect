<?php
namespace Ewave\Collect\Model;

use Ewave\Collect\Api\ApplyDeliveryInterface;

/**
 * Class ApplyDelivery
 * @package Ewave\Collect\Model
 */
class ApplyDelivery extends AbstractApplyShippingVariation implements ApplyDeliveryInterface
{
    /**
     * @return bool
     */
    public function applyDeliveryToAllItems()
    {
        return !empty($this->applyCollectParamsToAllItems());
    }
}
