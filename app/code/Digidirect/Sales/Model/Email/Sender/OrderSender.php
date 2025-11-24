<?php
namespace Digidirect\Sales\Model\Email\Sender;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * @var LoggerInterface
     */
    protected $customLogger;

    /**
     * OrderSender constructor.
     *
     * We don't override the constructor - let parent handle all dependencies
     */
    public function __construct(
        \Magento\Sales\Model\Order\Email\Container\OrderIdentity $identityContainer,
        \Magento\Sales\Model\Order\Email\Container\Template $templateContainer,
        \Magento\Sales\Model\Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        LoggerInterface $customLogger = null
    ) {
        $this->customLogger = $customLogger ?: $logger;
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

            $this->customLogger->info('Digidirect OrderSender: Processing order email', [
                'order_id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'shipping_method' => $shippingMethod
            ]);

            // Check if shipping method is store pickup/collection
            if ($shippingMethod === 'collect_collect') {
                $this->customLogger->info('Digidirect OrderSender: Switching to collection template for order #' . $order->getIncrementId());

                $this->templateContainer->setTemplateId(69);
            }
        } catch (\Exception $e) {
            $this->customLogger->error('Digidirect OrderSender: Error in prepareTemplate: ' . $e->getMessage());
        }
    }
}
