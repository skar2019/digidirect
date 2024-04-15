<?php
namespace Digidirect\Order\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderAddressExtension;
use Magento\Sales\Api\Data\OrderAddressInterface;

class OrderRepositoryInterface
{
    /**
     * @var OrderAddressExtensionInterfaceFactory
     */
    private $addressExtensionInterfaceFactory;
    protected $logger;
    public function __construct(
        OrderAddressExtensionInterfaceFactory $addressExtensionInterfaceFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
        $this->logger = $logger;
    }

    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        /**
         * @var OrderAddressInterface  $billingAddress
         */
        $this->logger->info('Test order API Override!');
        $initialShippingAddressUnitNumber = $order->getShippingAddress()->getUnitNumber();
        $this->logger->info('Test order API Override! -'.$initialShippingAddressUnitNumber);

        return $order;
    }
}
