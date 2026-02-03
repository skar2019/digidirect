<?php
namespace Digidirect\Collect\Plugin\Quote\Address;

use Magento\Quote\Model\Quote\Address;
use Psr\Log\LoggerInterface;

class PreventAutoShippingSelection
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeSetShippingMethod(
        Address $subject,
                $method
    ) {
        $this->logger->info('beforeSetShippingMethod called: ' . ($method ?? 'NULL'));

        if ($method === 'standard_standard') {
            $this->logger->info('BLOCKED standard_standard');
            return [null];
        }

        return [$method];
    }

    public function afterSetData(
        Address $subject,
                $result,
                $key,
                $value = null
    ) {
        if ($key === 'shipping_method' && $value === 'standard_standard') {
            $this->logger->info('BLOCKED standard_standard via setData');
            $subject->setData('shipping_method', null);
        }

        return $result;
    }
}
