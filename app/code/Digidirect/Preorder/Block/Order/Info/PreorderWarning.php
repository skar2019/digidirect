<?php

namespace Digidirect\PreOrder\Block\Order\Info;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

/**
 * Class PreorderWarning
 *
 * @package Digidirect\PreOrder\Block\Order\Info
 */
class PreorderWarning extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Digidirect\PreOrder\Helper\Config
     */
    protected $preorderConfigHelper;

    /**
     * @var \Digidirect\PreOrder\Api\OrderPreorderRepositoryInterface
     */
    protected $orderPreorderRepository;

    /**
     * PreorderWarning constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Digidirect\PreOrder\Helper\Config $preorderConfigHelper
     * @param \Digidirect\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Digidirect\PreOrder\Helper\Config $preorderConfigHelper,
        \Digidirect\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->preorderConfigHelper = $preorderConfigHelper;
        $this->orderPreorderRepository = $orderPreorderRepository;

        parent::__construct($context, $data);
    }

    /**
     * Get warning text
     *
     * @return string
     */
    public function getWarningText()
    {
        $order = $this->getOrder();
        if (!$order instanceof Order) {
            return '';
        }

        if (Order::STATE_COMPLETE == $order->getState()) {
            return '';
        }

        $orderId = $order->getId();

        try {
            if ($this->orderPreorderRepository->isOrderHasPreorderFlag($orderId)) {
                $preorder = $this->orderPreorderRepository->getByOrderId($orderId);
                if (!$warning = $preorder->getWarning()) {
                    $warning = $this->preorderConfigHelper->getOrderPreorderWarning();
                }
                return __($warning);
            }
        } catch (\Exception $e) {
            return '';
        }
        return '';
    }

    /**
     * Get order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }
}
