<?php

namespace Digidirect\PreOrder\Plugin;

/**
 * Class SalesOrderService
 *
 * @package Digidirect\PreOrder\Plugin
 */
class SalesOrderService
{
    /**
     * @var \Digidirect\PreOrder\Api\OrderPreorderManagementInterface
     */
    private $preorderManagement;

    /**
     * @var \Digidirect\PreOrder\Helper\Config
     */
    private $configHelper;

    /**
     * SalesOrderService constructor.
     *
     * @param \Digidirect\PreOrder\Api\OrderPreorderManagementInterface $preorderManagement
     * @param \Digidirect\PreOrder\Helper\Config $configHelper
     */
    public function __construct(
        \Digidirect\PreOrder\Api\OrderPreorderManagementInterface $preorderManagement,
        \Digidirect\PreOrder\Helper\Config $configHelper
    ) {
        $this->preorderManagement = $preorderManagement;
        $this->configHelper = $configHelper;
    }

    /**
     * Process new orders: @see \Magento\Sales\Model\Service\OrderService::place()
     *
     * @param \Magento\Sales\Model\Service\OrderService $subject
     * @param \Magento\Sales\Api\Data\OrderInterface $result
     * @return \Magento\Sales\Api\Data\OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        \Magento\Sales\Model\Service\OrderService $subject,
        \Magento\Sales\Api\Data\OrderInterface $result
    ) {
        if ($this->configHelper->preordersEnabled()) {
            $this->preorderManagement->processNewPreOrder($result);
        }

        return $result;
    }
}
