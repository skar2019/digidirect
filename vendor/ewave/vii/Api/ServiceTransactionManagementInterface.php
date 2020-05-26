<?php

namespace Ewave\Vii\Api;

interface ServiceTransactionManagementInterface
{
    /**
     * @param int $quoteId
     * @param null|\Magento\Sales\Api\Data\OrderInterface $order
     * @param bool $forceRemove
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function reversePreviousTransaction($quoteId, $order = null, $forceRemove = false);

    /**
     * @param \Ewave\Vii\Model\Service\Adapter $service
     * @return bool
     */
    public function pushUndoProcessToQueue($service);
}
