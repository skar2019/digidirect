<?php

namespace Digidirect\PreOrder\Api;

/**
 * Interface OrderPreorderManagementInterface
 *
 * @package Digidirect\PreOrder\Api
 */
interface OrderPreorderManagementInterface
{
    /**
     * Process new order actions
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    public function processNewPreOrder(\Magento\Sales\Api\Data\OrderInterface $order);
}
