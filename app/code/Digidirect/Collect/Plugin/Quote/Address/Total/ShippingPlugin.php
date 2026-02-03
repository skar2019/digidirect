<?php
namespace Digidirect\Collect\Plugin\Quote\Address\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Psr\Log\LoggerInterface;

class ShippingPlugin
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeCollect(
        \Magento\Quote\Model\Quote\Address\Total\Shipping $subject,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $this->logger->info('Shipping total collector - method: ' . ($address->getShippingMethod() ?? 'NULL'));

        return [$quote, $shippingAssignment, $total];
    }

    public function afterCollect(
        \Magento\Quote\Model\Quote\Address\Total\Shipping $subject,
                                                          $result,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $this->logger->info('After shipping total collect - method: ' . ($address->getShippingMethod() ?? 'NULL'));

        return $result;
    }
}
