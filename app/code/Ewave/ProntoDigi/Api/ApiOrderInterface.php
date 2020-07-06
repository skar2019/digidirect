<?php

namespace Ewave\ProntoDigi\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface ApiOrderInterface
{
    /**
     * @param OrderInterface $entity
     * @return OrderInterface
     */
    public function update(OrderInterface $entity);
}
