<?php
namespace Digidirect\Order\Plugin\Magento\Sales\Api;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class OrderGet
{
    protected $orderExtensionFactory;
    protected $logger;
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->logger = $logger;
    }

    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $this->logger->info('Test order API Override!');

        $extensionAttributes = $resultOrder->getExtensionAttributes();
        if ($extensionAttributes && $extensionAttributes->getUnitNumber()) {
            return $resultOrder;
        }

        $initialShippingAddressUnitNumber = $resultOrder->getShippingAddress()->getUnitNumber();
        $street = $resultOrder->getShippingAddress()->getStreet();
        $newstreet = $initialShippingAddressUnitNumber . " ". $street[0];
        $resultOrder->getShippingAddress()->setStreet(array($newstreet));
        $resultOrder->setShippingAddress()->setStreet(array($newstreet));
        $this->logger->info('Test order API Override! -'.$newstreet);
        /** @var \Magento\Sales\Api\Data\OrderExtension $orderExtension */

//        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
//        $orderExtension->setUnitNumber($initialShippingAddressUnitNumber);
//        $resultOrder->setExtensionAttributes($orderExtension);

        return $resultOrder;

    }

    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection $resultOrder
    ) {
        /** @var  $order */
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }
}
