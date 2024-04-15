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
    protected $orderExtension;
    public function __construct(
        OrderAddressExtensionInterfaceFactory $addressExtensionInterfaceFactory,
        \Magento\Sales\Api\Data\OrderExtension $orderExtension,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
        $this->logger = $logger;
        $this->orderExtension = $orderExtension;
    }

    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        /**
         * @var OrderAddressInterface  $billingAddress
         */
        $this->logger->info('Test order API Override!');
        //Set unit number for Shipping Address

        $initialShippingAddressUnitNumber = $order->getShippingAddress()->getUnitNumber();
        $this->logger->info('Test order API Override! -'.$initialShippingAddressUnitNumber);
        $extensionAttributes= $order->getExtensionAttributes();
        if ($extensionAttributes === null)
        {
            $extensionAttributes = $this->orderExtension;
        }
        $extensionAttributes->setData('unit_number',$initialShippingAddressUnitNumber);

        $order->setExtensionAttributes($extensionAttributes);
        return $order;
    }
}
