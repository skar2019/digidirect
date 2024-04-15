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

        return $order;
    }
}
