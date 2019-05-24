<?php

namespace Ewave\CriteoOneTag\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetOrderIdPageViewObserver
 *
 * @package Ewave\CriteoOneTag\Observer
 */
class SetOrderIdPageViewObserver implements ObserverInterface
{
    /**
     * @var \Magento\GoogleTagManager\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @param \Magento\GoogleTagManager\Helper\Data $helper
     * @param \Magento\Framework\App\ViewInterface $view
     */
    public function __construct(
        \Magento\GoogleTagManager\Helper\Data $helper,
        \Magento\Framework\App\ViewInterface $view
    ) {
        $this->helper = $helper;
        $this->view = $view;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getData('order_ids');
        if (empty($orderIds) || !is_array($orderIds)) {
            return $this;
        }
        /** @var \Ewave\CriteoOneTag\Block\Order $block */
        $block = $this->view->getLayout()->getBlock('ewave.criteo.order');
        if ($block) {
            $block->setData('order_ids', $orderIds);
        }
        return $this;
    }
}
