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

    public function __construct(
        OrderAddressExtensionInterfaceFactory $addressExtensionInterfaceFactory
    ) {
        $this->addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
    }

    public function afterGet(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        OrderInterface $order
    ) {
        /**
         * @var OrderAddressInterface  $billingAddress
         */
        $billingAddress = $order->getBillingAddress();

        $billingAddressExtensionAttributes = (null !== $billingAddress->getExtensionAttributes())?
            $billingAddress->getExtensionAttributes():
            $this->addressExtensionInterfaceFactory->create();
        $billingAddressExtensionAttributes->setUnitNumber($billingAddress->getUnitNumber());

        $billingAddress->setExtensionAttributes($billingAddressExtensionAttributes);

        if (!$order->getIsVirtual()) {
            $shippingAddress = $order->getShippingAddress();
            $shippingAddressExtensionAttributes = (null!== $shippingAddress->getExtensionAttributes())?
                $shippingAddress->getExtensionAttributes():
                $this->addressExtensionInterfaceFactory->create();

            $shippingAddressExtensionAttributes->setUnitNumber($shippingAddress->getUnitNumber());
            $shippingAddress->setExtensionAttributes($shippingAddressExtensionAttributes);
        }

        return $order;
    }
}
