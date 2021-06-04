<?php

namespace Ewave\PreOrder\Block\Order\Info;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;

/**
 * Class PreorderWarning
 *
 * @package Ewave\PreOrder\Block\Order\Info
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
     * @var \Ewave\PreOrder\Helper\Config
     */
    protected $preorderConfigHelper;

    /**
     * @var \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface
     */
    protected $orderPreorderRepository;

    /**
     * PreorderWarning constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ewave\PreOrder\Helper\Config $preorderConfigHelper
     * @param \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ewave\PreOrder\Helper\Config $preorderConfigHelper,
        \Ewave\PreOrder\Api\OrderPreorderRepositoryInterface $orderPreorderRepository,
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
