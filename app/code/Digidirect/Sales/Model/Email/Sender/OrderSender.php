<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\Order\Email\SenderBuilderFactory;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * OrderSender constructor.
     *
     * @param OrderIdentity $identityContainer
     * @param Template $templateContainer
     * @param SenderBuilderFactory $senderBuilderFactory
     * @param LoggerInterface $logger
     * @param Renderer $addressRenderer
     * @param PaymentHelper $paymentHelper
     * @param CollectionFactory $itemCollectionFactory
     * @param ManagerInterface $eventManager
     */
    public function __construct(
        OrderIdentity $identityContainer,
        Template $templateContainer,
        SenderBuilderFactory $senderBuilderFactory,
        LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        CollectionFactory $itemCollectionFactory,
        ManagerInterface $eventManager
    ) {
        $this->logger = $logger;
        parent::__construct(
            $identityContainer,
            $templateContainer,
            $senderBuilderFactory,
            $logger,
            $addressRenderer,
            $paymentHelper,
            $itemCollectionFactory,
            $eventManager
        );
    }

    /**
     * Prepare email template with custom logic for shipping method
     *
     * @param Order $order
     * @return void
     */
    protected function prepareTemplate(Order $order)
    {
        // Call parent first to set up default template
        parent::prepareTemplate($order);

        try {
            // Get shipping method from order
            $shippingMethod = $order->getShippingMethod();

            $this->logger->info('OrderSender: Processing order email', [
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'shipping_method' => $shippingMethod,
                'payment_method' => $order->getPayment()->getMethod()
            ]);

            // Check if shipping method is store pickup/collection
            if ($shippingMethod === 'collect_collect') {
                $this->logger->info('OrderSender: Switching to collection template', [
                    'order_id' => $order->getId(),
                    'template_id' => 69
                ]);

                $this->templateContainer->setTemplateId(69);
            }
        } catch (\Exception $e) {
            $this->logger->error('OrderSender: Error preparing template', [
                'order_id' => $order->getId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Don't throw exception, let the email send with default template
        }
    }
}
