<?php
namespace Digidirect\Sales\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Email\Model\Template\Config as TemplateConfig;
use Psr\Log\LoggerInterface;

class ChangeOrderEmailTemplateObserver implements ObserverInterface
{
    /**
     * @var TemplateConfig
     */
    protected $templateConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param TemplateConfig $templateConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        TemplateConfig $templateConfig,
        LoggerInterface $logger
    ) {
        $this->templateConfig = $templateConfig;
        $this->logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $transport = $observer->getData('transportObject');
        if (!$transport) {
            return;
        }

        $order = $transport->getData('order');
        if (!$order instanceof Order) {
            return;
        }

        $shippingMethod = $order->getShippingMethod();

        $identityContainer = $observer->getData('sender')->getIdentityContainer();

        $templateId = 69; // Your Click & Collect template ID

        // Check if template exists for this store
        $storeId = $order->getStoreId();
        $templateConfig = $this->templateConfig->getTemplateByConfigPath('sales_email/order/template', $storeId);

        if ($templateId) {
            $identityContainer->setTemplateId($templateId);
            $identityContainer->setStore($order->getStore());

            $this->logger->info('Click & Collect email template applied', [
                'order_id' => $order->getIncrementId(),
                'shipping_method' => $shippingMethod,
                'template_id' => $templateId
            ]);
        } else {
            $this->logger->warning('Click & Collect template ID not found', [
                'order_id' => $order->getIncrementId(),
                'shipping_method' => $shippingMethod,
                'template_id' => $templateId
            ]);
        }
    }
}
