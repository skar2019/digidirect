<?php

namespace Ewave\PreOrder\Api;

/**
 * Interface OrderPreorderManagementInterface
 *
 * @package Ewave\PreOrder\Api
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
