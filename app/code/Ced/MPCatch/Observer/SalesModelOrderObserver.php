<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_MPCatch
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\MPCatch\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesModelOrderObserver implements ObserverInterface
{

    /** @var \Ced\MPCatch\Helper\Logger  */
    public $logger;

    /**
     * Object Manager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * ProductSaveAfter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\MPCatch\Helper\Logger $logger
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
    }

    /**
     * Catalog product save after event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $this->product = $this->objectManager->create('Magento\Catalog\Model\ProductFactory');
            $this->productHelper = $this->objectManager->create('\Ced\MPCatch\Helper\Product');

            $this->logger->addError('Sales Order Observer: success', ['path' => __METHOD__, 'Response' => 'enter']);
            $event = $observer->getEvent();
            $order = $event->getOrder();
            foreach ($order->getAllItems() as $item) {
                $productId = $item->getProductId();
                $product = $this->product->create()->load($productId);
                if($product->getMpcatchProfileId())
                    $this->productHelper->updatePriceInventory([$productId]);
            }
            $this->logger->addError('Sales Order Observer: success', ['path' => __METHOD__, 'Response' => 'exit']);
        } catch (\Exception $e) {
            $this->logger->addError('Sales Order Observer: success', ['path' => __METHOD__, 'exception' => $e->getMessage()]);
            return $observer;
        }
        return $observer;
    }
}
