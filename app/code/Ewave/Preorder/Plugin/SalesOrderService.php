<?php

namespace Ewave\PreOrder\Plugin;

/**
 * Class SalesOrderService
 *
 * @package Ewave\PreOrder\Plugin
 */
class SalesOrderService
{
    /**
     * @var \Ewave\PreOrder\Api\OrderPreorderManagementInterface
     */
    private $preorderManagement;

    /**
     * @var \Ewave\PreOrder\Helper\Config
     */
    private $configHelper;

    /**
     * SalesOrderService constructor.
     *
     * @param \Ewave\PreOrder\Api\OrderPreorderManagementInterface $preorderManagement
     * @param \Ewave\PreOrder\Helper\Config $configHelper
     */
    public function __construct(
        \Ewave\PreOrder\Api\OrderPreorderManagementInterface $preorderManagement,
        \Ewave\PreOrder\Helper\Config $configHelper
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
