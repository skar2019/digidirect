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
        // Log all attempts to set shipping method
        $this->logger->info('Attempting to set shipping method: ' . ($method ?? 'NULL'));

        // Block standard_standard from being set
        if ($method === 'standard_standard') {
            $this->logger->info('BLOCKED standard_standard from being set');
            return [null];
        }

        return [$method];
    }
}
