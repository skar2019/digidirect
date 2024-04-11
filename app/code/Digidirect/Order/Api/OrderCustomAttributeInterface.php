<?php

namespace Digidirect\Order\Api;

interface OrderCustomAttributeInterface
{
    /**
     * Return custom attribute of order shipping address data
     *
     * @param int $orderId
     * @return string
     */
    public function getUnitNumber($orderId);
}
